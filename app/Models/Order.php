<?php
namespace App\Models;

use App\Core\Database;

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDailySales() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(final_amount), 0) as total_sales,
                COALESCE(SUM(tax_amount), 0) as total_tax
            FROM orders 
            WHERE DATE(created_at) = CURDATE()
        ");
        return $this->db->single();
    }

    public function getMTDSales() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(final_amount), 0) as total_sales,
                COALESCE(SUM(tax_amount), 0) as total_tax
            FROM orders 
            WHERE 
                YEAR(created_at) = YEAR(CURDATE()) 
                AND MONTH(created_at) = MONTH(CURDATE())
        ");
        return $this->db->single();
    }

    public function getYTDSales() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(final_amount), 0) as total_sales,
                COALESCE(SUM(tax_amount), 0) as total_tax
            FROM orders 
            WHERE YEAR(created_at) = YEAR(CURDATE())
        ");
        return $this->db->single();
    }

    public function getRecentOrders($limit = 5) {
        $this->db->query("
            SELECT 
                o.id,
                o.invoice_number,
                o.final_amount,
                o.created_at,
                u.name as cashier_name,
                COUNT(oi.id) as total_items
            FROM orders o
            JOIN users u ON o.created_by = u.id
            JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Insert order
            $this->db->query("
                INSERT INTO orders (
                    id, invoice_number, total_amount, tax_amount, 
                    final_amount, payment_amount, change_amount, created_by
                ) VALUES (
                    :id, :invoice_number, :total_amount, :tax_amount,
                    :final_amount, :payment_amount, :change_amount, :created_by
                )
            ");

            $this->db->bind(':id', $data['id']);
            $this->db->bind(':invoice_number', $data['invoice_number']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':tax_amount', $data['tax_amount']);
            $this->db->bind(':final_amount', $data['final_amount']);
            $this->db->bind(':payment_amount', $data['payment_amount']);
            $this->db->bind(':change_amount', $data['change_amount']);
            $this->db->bind(':created_by', $data['created_by']);

            $this->db->execute();

            // Insert order items
            foreach ($data['items'] as $item) {
                $this->db->query("
                    INSERT INTO order_items (
                        id, order_id, product_id, quantity, price, subtotal
                    ) VALUES (
                        :id, :order_id, :product_id, :quantity, :price, :subtotal
                    )
                ");

                $this->db->bind(':id', $item['id']);
                $this->db->bind(':order_id', $data['id']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);

                $this->db->execute();

                // Update product stock
                $this->db->query("
                    UPDATE products 
                    SET stock = stock - :quantity 
                    WHERE id = :product_id
                ");
                
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
