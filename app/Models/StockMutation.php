<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class StockMutation {
    private $db;

    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_RETURN = 'return';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO stock_mutations (
                product_id, type, quantity, before_stock, after_stock, 
                reference_id, notes, created_by, created_at
            ) VALUES (
                :product_id, :type, :quantity, :before_stock, :after_stock,
                :reference_id, :notes, :created_by, NOW()
            )";

            $params = [
                ':product_id' => $data['product_id'],
                ':type' => $data['type'],
                ':quantity' => $data['quantity'],
                ':before_stock' => $data['before_stock'],
                ':after_stock' => $data['after_stock'],
                ':reference_id' => $data['reference_id'] ?? null,
                ':notes' => $data['notes'] ?? null,
                ':created_by' => $data['created_by']
            ];

            // Prepare and execute the stock mutation query
            $this->db->query($query);
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }
            $this->db->execute();

            // Update product stock
            $updateQuery = "UPDATE products SET stock = :after_stock WHERE id = :product_id";
            $this->db->query($updateQuery)
                ->bind(':after_stock', $data['after_stock'])
                ->bind(':product_id', $data['product_id'])
                ->execute();

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getProductMutations($productId, $limit = 50) {
        $query = "SELECT 
            sm.*,
            u.name as created_by_name,
            p.name as product_name,
            p.code as product_code
        FROM stock_mutations sm
        JOIN users u ON sm.created_by = u.id
        JOIN products p ON sm.product_id = p.id
        WHERE sm.product_id = :product_id
        ORDER BY sm.created_at DESC
        LIMIT :limit";

        $this->db->query($query);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getMutationsByDate($startDate, $endDate, $type = null) {
        $query = "SELECT sm.*, p.name as product_name, p.code as product_code, p.stock as current_stock, 
                 u.name as created_by_name
                 FROM stock_mutations sm 
                 LEFT JOIN products p ON sm.product_id = p.id 
                 LEFT JOIN users u ON sm.created_by = u.id 
                 WHERE DATE(sm.created_at) BETWEEN :start_date AND :end_date";

        if ($type) {
            $query .= " AND sm.type = :type";
        }

        $query .= " ORDER BY sm.created_at DESC";

        $this->db->query($query);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        if ($type) {
            $this->db->bind(':type', $type);
        }

        return $this->db->resultSet();
    }

    public function validateStockChange($productId, $quantity, $type) {
        $query = "SELECT stock FROM products WHERE id = :product_id";
        $this->db->query($query);
        $this->db->bind(':product_id', $productId);
        $product = $this->db->single();

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $currentStock = $product->stock;
        $newStock = $currentStock;

        switch ($type) {
            case self::TYPE_SALE:
            case self::TYPE_RETURN:
                $newStock = $currentStock - $quantity;
                if ($newStock < 0) {
                    throw new \Exception('Insufficient stock');
                }
                break;
            case self::TYPE_PURCHASE:
            case self::TYPE_ADJUSTMENT:
                $newStock = $currentStock + $quantity;
                break;
        }

        return [
            'before_stock' => $currentStock,
            'after_stock' => $newStock
        ];
    }
}
