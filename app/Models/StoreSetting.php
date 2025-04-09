<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class StoreSetting {
    private $db;
    private $table = 'store_settings';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getSettings() {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT 1");
        return $this->db->single();
    }

    public function getTaxRate() {
        $settings = $this->getSettings();
        return $settings ? (float)$settings->tax_rate : 0;
    }
    
    public function save($data, $userId) {
        try {
            $this->db->beginTransaction();

            $settings = $this->getSettings();
            if ($settings) {
                // Update existing settings
                $this->db->query("UPDATE {$this->table} SET 
                    store_name = :store_name,
                    address = :address,
                    phone = :phone,
                    email = :email,
                    logo = :logo,
                    tax_rate = :tax_rate,
                    service_charge = :service_charge,
                    printer_name = :printer_name,
                    printer_type = :printer_type,
                    thank_you_message = :thank_you_message,
                    updated_by = :updated_by,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id");
                
                $this->db->bind(':id', $settings->id);
                $this->db->bind(':updated_by', $userId);
            } else {
                // Insert new settings
                $this->db->query("INSERT INTO {$this->table} 
                    (store_name, address, phone, email, logo, tax_rate, 
                    service_charge, printer_name, printer_type, thank_you_message, created_by) 
                    VALUES 
                    (:store_name, :address, :phone, :email, :logo, :tax_rate, 
                    :service_charge, :printer_name, :printer_type, :thank_you_message, :created_by)");
                
                $this->db->bind(':created_by', $userId);
            }

            // Bind common parameters
            $this->db->bind(':store_name', $data['store_name']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':logo', $data['logo']);
            $this->db->bind(':tax_rate', $data['tax_rate']);
            $this->db->bind(':service_charge', $data['service_charge']);
            $this->db->bind(':printer_name', $data['printer_name']);
            $this->db->bind(':printer_type', $data['printer_type']);
            $this->db->bind(':thank_you_message', $data['thank_you_message']);

            $result = $this->db->execute();
            $this->db->commit();
            return $result;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in StoreSetting::save(): " . $e->getMessage());
            return false;
        }
    }
    
    private function trackChanges($oldSettings, $newData) {
        $changes = [];
        $fields = [
            'store_name' => ['label' => 'Store Name', 'type' => 'text'],
            'address' => ['label' => 'Store Address', 'type' => 'text'],
            'phone' => ['label' => 'Phone Number', 'type' => 'text'],
            'email' => ['label' => 'Email Address', 'type' => 'text'],
            'tax_rate' => ['label' => 'Tax Rate', 'type' => 'percentage'],
            'service_charge' => ['label' => 'Service Charge', 'type' => 'percentage'],
            'printer_name' => ['label' => 'Printer Name', 'type' => 'text'],
            'printer_type' => ['label' => 'Printer Type', 'type' => 'text'],
            'thank_you_message' => ['label' => 'Thank You Message', 'type' => 'text']
        ];

        foreach ($fields as $field => $info) {
            if (isset($newData[$field]) && $oldSettings->$field != $newData[$field]) {
                $oldValue = $oldSettings->$field;
                $newValue = $newData[$field];

                if ($info['type'] === 'percentage') {
                    $oldValue = number_format($oldValue, 2) . '%';
                    $newValue = number_format($newValue, 2) . '%';
                }

                if (empty($oldValue)) {
                    $changes[] = "Added {$info['label']}: {$newValue}";
                } elseif (empty($newValue)) {
                    $changes[] = "Removed {$info['label']}: {$oldValue}";
                } else {
                    $changes[] = "Updated {$info['label']} from {$oldValue} to {$newValue}";
                }
            }
        }

        // Check for logo changes
        if (isset($newData['logo']) && $oldSettings->logo != $newData['logo']) {
            if (empty($oldSettings->logo)) {
                $changes[] = "Added new store logo";
            } elseif (empty($newData['logo'])) {
                $changes[] = "Removed store logo";
            } else {
                $changes[] = "Updated store logo";
            }
        }

        return $changes;
    }

    private function logChanges($userId, $changes) {
        if (empty($changes)) return;

        $this->db->query("INSERT INTO audit_logs (user_id, module, action, details) 
                         VALUES (:user_id, 'settings', 'update', :details)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':details', implode('; ', $changes));
        $this->db->execute();
    }
    
    public function update($id, $data, $userId) {
        try {
            $this->db->query("UPDATE {$this->table} SET 
                    store_name = :store_name,
                    address = :address,
                    phone = :phone,
                    email = :email,
                    logo = :logo,
                    tax_rate = :tax_rate,
                    service_charge = :service_charge,
                    printer_name = :printer_name,
                    printer_type = :printer_type,
                    thank_you_message = :thank_you_message,
                    updated_by = :updated_by,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id");
            
            $data['id'] = $id;
            $data['updated_by'] = $userId;

            // Bind all parameters
            foreach ($data as $key => $value) {
                $this->db->bind(":{$key}", $value);
            }

            return $this->db->execute();
        } catch (\Exception $e) {
            error_log("Error in StoreSetting::update(): " . $e->getMessage());
            return false;
        }
    }
    
    public function getHistory() {
        try {
            $this->db->query("SELECT al.*, u.username, u.name as user_name
                    FROM audit_logs al
                    LEFT JOIN users u ON al.user_id = u.id
                    WHERE al.module = 'settings'
                    ORDER BY al.created_at DESC");
            
            return $this->db->resultSet();
        } catch (\Exception $e) {
            error_log("Error in StoreSetting::getHistory(): " . $e->getMessage());
            return [];
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