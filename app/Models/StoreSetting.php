<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class StoreSetting {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function get() {
        try {
            $this->db->query("SELECT * FROM store_settings LIMIT 1");
            $result = $this->db->single();
            
            // Return default settings if no settings found
            if (!$result) {
                return (object) [
                    'store_name' => 'POS System',
                    'address' => 'Store Address',
                    'phone' => '-',
                    'tax_percentage' => 11,
                    'receipt_header' => 'Thank you for your purchase!',
                    'receipt_footer' => 'Please come again!'
                ];
            }
            
            return $result;
        } catch (\PDOException $e) {
            // If the table doesn't exist yet, return default settings
            return (object) [
                'store_name' => 'POS System',
                'address' => 'Store Address',
                'phone' => '-',
                'tax_percentage' => 11,
                'receipt_header' => 'Thank you for your purchase!',
                'receipt_footer' => 'Please come again!'
            ];
        }
    }
    
    public function save($data) {
        try {
            // Check if settings already exist
            $existing = $this->get();
            
            if (isset($existing->id)) {
                // Update
                $this->db->query("UPDATE store_settings SET 
                    store_name = :store_name,
                    address = :address,
                    phone = :phone,
                    tax_percentage = :tax_percentage,
                    receipt_header = :receipt_header,
                    receipt_footer = :receipt_footer,
                    updated_at = NOW()
                WHERE id = :id");
                
                $this->db->bind(':id', $existing->id);
            } else {
                // Insert
                $this->db->query("INSERT INTO store_settings (
                    store_name, address, phone, tax_percentage, 
                    receipt_header, receipt_footer, created_at, updated_at
                ) VALUES (
                    :store_name, :address, :phone, :tax_percentage, 
                    :receipt_header, :receipt_footer, NOW(), NOW()
                )");
            }
            
            $this->db->bind(':store_name', $data['store_name']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':tax_percentage', $data['tax_percentage']);
            $this->db->bind(':receipt_header', $data['receipt_header']);
            $this->db->bind(':receipt_footer', $data['receipt_footer']);
            
            return $this->db->execute();
        } catch (\PDOException $e) {
            // Create table if it doesn't exist
            $this->createTable();
            
            // Try again
            return $this->save($data);
        }
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS store_settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            store_name VARCHAR(100) NOT NULL,
            address TEXT,
            phone VARCHAR(20),
            tax_percentage DECIMAL(5,2) DEFAULT 11.00,
            receipt_header TEXT,
            receipt_footer TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->query($sql);
        $this->db->execute();
    }
} 