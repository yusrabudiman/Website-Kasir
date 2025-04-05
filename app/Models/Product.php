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
}
