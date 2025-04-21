<?php
namespace App\Models;

use App\Core\Database;

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getTopSellingProducts($limit = 5) {
        $this->db->query("
            SELECT 
                p.id,
                p.name,
                p.code,
                p.price,
                p.stock,
                COUNT(oi.id) as times_sold,
                SUM(oi.quantity) as total_quantity_sold
            FROM products p
            LEFT JOIN order_items oi ON p.id = oi.product_id
            GROUP BY p.id
            ORDER BY total_quantity_sold DESC
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function getLowStockProducts($threshold = 10) {
        $this->db->query("
            SELECT id, code, name, stock, price
            FROM products
            WHERE stock <= :threshold
            ORDER BY stock ASC
        ");
        $this->db->bind(':threshold', $threshold);
        return $this->db->resultSet();
    }

    public function getInventoryReport($stockStatus = 'all', $sortBy = 'name', $order = 'asc') {
        // Build the WHERE clause based on stock status
        $where = '';
        if ($stockStatus === 'low_stock') {
            // Get low stock threshold from settings or use default 10
            $this->db->query("SELECT low_stock_threshold FROM store_settings LIMIT 1");
            $settings = $this->db->single();
            $threshold = $settings && $settings->low_stock_threshold ? $settings->low_stock_threshold : 10;
            $where = "WHERE p.stock > 0 AND p.stock <= {$threshold}";
        } elseif ($stockStatus === 'out_of_stock') {
            $where = "WHERE p.stock <= 0";
        } elseif ($stockStatus === 'in_stock') {
            $this->db->query("SELECT low_stock_threshold FROM store_settings LIMIT 1");
            $settings = $this->db->single();
            $threshold = $settings && $settings->low_stock_threshold ? $settings->low_stock_threshold : 10;
            $where = "WHERE p.stock > {$threshold}";
        }

        // Build the ORDER BY clause based on sort parameter
        $orderBy = match($sortBy) {
            'stock' => "p.stock",
            'value' => "p.stock * p.price",
            default => "p.name"
        };

        // Ensure order is valid
        $orderDir = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $this->db->query("
            SELECT 
                p.id,
                p.code,
                p.name,
                p.description,
                p.price,
                p.stock,
                COALESCE(p.min_stock, 5) as min_stock, 
                p.created_at
            FROM products p
            {$where}
            ORDER BY {$orderBy} {$orderDir}
        ");

        return $this->db->resultSet();
    }

    public function getInventorySummary() {
        // Get low stock threshold from settings or use default
        $this->db->query("SELECT low_stock_threshold FROM store_settings LIMIT 1");
        $settings = $this->db->single();
        $threshold = $settings && $settings->low_stock_threshold ? $settings->low_stock_threshold : 10;

        $this->db->query("
            SELECT 
                COUNT(*) as total_products,
                SUM(price * stock) as total_value,
                SUM(CASE WHEN stock > 0 AND stock <= {$threshold} THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN stock > {$threshold} THEN 1 ELSE 0 END) as in_stock
            FROM products
        ");

        return $this->db->single();
    }

    public function create($data) {
        $this->db->query("
            INSERT INTO products (id, code, name, description, price, stock) 
            VALUES (:id, :code, :name, :description, :price, :stock)
        ");

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);

        return $this->db->execute();
    }

    public function update($data) {
        $this->db->query("
            UPDATE products 
            SET name = :name,
                description = :description,
                price = :price,
                stock = :stock
            WHERE id = :id
        ");

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);

        return $this->db->execute();
    }

    public function findById($id) {
        $this->db->query("SELECT * FROM products WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function findByCode($code) {
        $this->db->query("SELECT * FROM products WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM products ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function searchAvailable($search) {
        $this->db->query("
            SELECT id, code, name, price, stock 
            FROM products 
            WHERE (LOWER(name) LIKE :search_name OR LOWER(code) LIKE :search_code)
            AND stock > 0
            ORDER BY name ASC
        ");
        
        $searchTerm = "%" . strtolower($search) . "%";
        $this->db->bind(':search_name', $searchTerm);
        $this->db->bind(':search_code', $searchTerm);
        
        // Debug log
        error_log("Search term: " . $search);
        
        return $this->db->resultSet();
    }

    public function search($search) {
        $this->db->query("
            SELECT id, code, name, description, price, stock 
            FROM products 
            WHERE LOWER(name) LIKE :search_name 
               OR LOWER(code) LIKE :search_code
            ORDER BY name ASC
        ");
        
        $searchTerm = "%" . strtolower($search) . "%";
        $this->db->bind(':search_name', $searchTerm);
        $this->db->bind(':search_code', $searchTerm);
        
        // Debug log
        error_log("Search term: " . $search);
        
        return $this->db->resultSet();
    }

    public function getAllAvailable() {
        $this->db->query("
            SELECT id, code, name, price, stock 
            FROM products 
            WHERE stock > 0
            ORDER BY name ASC
        ");
        
        return $this->db->resultSet();
    }
}
