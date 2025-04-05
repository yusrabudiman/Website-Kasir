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
                    'email' => 'store@example.com',
                    'tax_percentage' => 11.00,
                    'currency_symbol' => 'Rp',
                    'low_stock_threshold' => 10,
                    'receipt_footer' => 'Thank you for your purchase! Please come again.'
                ];
            }
            
            return $result;
        } catch (\PDOException $e) {
            // If the table doesn't exist yet, return default settings
            return (object) [
                'store_name' => 'POS System',
                'address' => 'Store Address',
                'phone' => '-',
                'email' => 'store@example.com',
                'tax_percentage' => 11.00,
                'currency_symbol' => 'Rp',
                'low_stock_threshold' => 10,
                'receipt_footer' => 'Thank you for your purchase! Please come again.'
            ];
        }
    }
    
    public function save($data) {
        try {
            // Make sure table schema is up to date
            $this->updateTableSchema();
            
            // Check if settings already exist
            $this->db->query("SELECT COUNT(*) as count FROM store_settings");
            $result = $this->db->single();
            $exists = $result && $result->count > 0;
            
            if ($exists) {
                // Use a simplified update approach - update all fields at once
                $this->db->query("UPDATE store_settings SET 
                    store_name = :store_name,
                    address = :address,
                    phone = :phone,
                    email = :email,
                    tax_percentage = :tax_percentage,
                    currency_symbol = :currency_symbol,
                    low_stock_threshold = :low_stock_threshold,
                    receipt_footer = :receipt_footer,
                    updated_at = NOW()");
            } else {
                // Insert
                $this->db->query("INSERT INTO store_settings (
                    store_name, address, phone, email, tax_percentage, 
                    currency_symbol, low_stock_threshold, receipt_footer, 
                    created_at, updated_at
                ) VALUES (
                    :store_name, :address, :phone, :email, :tax_percentage, 
                    :currency_symbol, :low_stock_threshold, :receipt_footer, 
                    NOW(), NOW()
                )");
            }
            
            // Bind parameters once for all queries
            $this->db->bind(':store_name', $data['store_name']);
            $this->db->bind(':address', $data['store_address'] ?? '');
            $this->db->bind(':phone', $data['store_phone'] ?? '');
            $this->db->bind(':email', $data['store_email'] ?? null);
            $this->db->bind(':tax_percentage', $data['tax_percentage'] ?? 11.00);
            $this->db->bind(':currency_symbol', $data['currency_symbol'] ?? 'Rp');
            $this->db->bind(':low_stock_threshold', $data['low_stock_threshold'] ?? 10);
            $this->db->bind(':receipt_footer', $data['receipt_footer'] ?? 'Thank you for your purchase!');
            
            // Handle store_logo separately if provided
            if (!empty($data['store_logo']) && $exists) {
                $this->db->query("UPDATE store_settings SET store_logo = :logo");
                $this->db->bind(':logo', $data['store_logo']);
                $this->db->execute();
            }
            
            return $this->db->execute();
        } catch (\PDOException $e) {
            // Create table if it doesn't exist and try again
            $this->createTable();
            
            // Insert default values
            $this->db->query("INSERT INTO store_settings (
                store_name, address, phone, email, tax_percentage, 
                currency_symbol, low_stock_threshold, receipt_footer,
                created_at, updated_at
            ) VALUES (
                :store_name, :address, :phone, :email, :tax_percentage, 
                :currency_symbol, :low_stock_threshold, :receipt_footer,
                NOW(), NOW()
            )");
            
            $this->db->bind(':store_name', $data['store_name'] ?? 'POS System');
            $this->db->bind(':address', $data['store_address'] ?? 'Store Address');
            $this->db->bind(':phone', $data['store_phone'] ?? '-');
            $this->db->bind(':email', $data['store_email'] ?? 'store@example.com');
            $this->db->bind(':tax_percentage', $data['tax_percentage'] ?? 11.00);
            $this->db->bind(':currency_symbol', $data['currency_symbol'] ?? 'Rp');
            $this->db->bind(':low_stock_threshold', $data['low_stock_threshold'] ?? 10);
            $this->db->bind(':receipt_footer', $data['receipt_footer'] ?? 'Thank you for your purchase!');
            
            return $this->db->execute();
        }
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS store_settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            store_name VARCHAR(100) NOT NULL,
            address TEXT,
            phone VARCHAR(20),
            email VARCHAR(100),
            tax_percentage DECIMAL(5,2) DEFAULT 11.00,
            store_logo VARCHAR(255),
            currency_symbol VARCHAR(10) DEFAULT 'Rp',
            low_stock_threshold INT DEFAULT 10,
            receipt_footer TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->query($sql);
        $this->db->execute();
    }
    
    private function updateTableSchema() {
        try {
            // Use a direct approach instead of looping through columns
            // This is safer and less memory intensive
            $this->db->query("SHOW TABLES LIKE 'store_settings'");
            $tableExists = !empty($this->db->resultSet());
            
            if (!$tableExists) {
                $this->createTable();
                return true;
            }
            
            // Simple check for one column to see if we need to update
            $this->db->query("SHOW COLUMNS FROM store_settings LIKE 'currency_symbol'");
            $hasCurrency = !empty($this->db->resultSet());
            
            // If currency_symbol doesn't exist, assume we need the full schema update
            if (!$hasCurrency) {
                $alterQueries = [
                    "ALTER TABLE store_settings ADD COLUMN email VARCHAR(100) AFTER phone",
                    "ALTER TABLE store_settings ADD COLUMN store_logo VARCHAR(255) AFTER tax_percentage",
                    "ALTER TABLE store_settings ADD COLUMN currency_symbol VARCHAR(10) DEFAULT 'Rp' AFTER store_logo",
                    "ALTER TABLE store_settings ADD COLUMN low_stock_threshold INT DEFAULT 10 AFTER currency_symbol"
                ];
                
                foreach ($alterQueries as $sql) {
                    try {
                        $this->db->query($sql);
                        $this->db->execute();
                    } catch (\PDOException $e) {
                        // Ignore errors if column already exists
                        continue;
                    }
                }
            }
            
            return true;
        } catch (\PDOException $e) {
            // Table doesn't exist, will be created with createTable()
            return false;
        }
    }
} 