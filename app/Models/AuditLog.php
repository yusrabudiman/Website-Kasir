<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use Ramsey\Uuid\Uuid;

class AuditLog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO audit_logs (id, user_id, module, action, details, ip_address) 
                    VALUES (:id, :user_id, :module, :action, :details, :ip_address)";
            
            $this->db->query($sql);
            $this->db->bind(':id', Uuid::uuid4()->toString());
            $this->db->bind(':user_id', $data['user_id'] ?? $_SESSION['user_id'] ?? null);
            $this->db->bind(':module', $data['module']);
            $this->db->bind(':action', $data['action']);
            $this->db->bind(':details', $data['details'] ?? '');
            $this->db->bind(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
            $this->db->execute();
            
            return true;
        } catch (\PDOException $e) {
            // Log error but don't interrupt the main application flow
            error_log('Audit log creation failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getLogs($startDate = null, $endDate = null, $userId = null, $module = null, $action = null) {
        $sql = "SELECT al.*, u.name as user_name 
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE 1=1";
        
        if ($startDate) {
            $sql .= " AND DATE(al.created_at) >= :start_date";
        }
        
        if ($endDate) {
            $sql .= " AND DATE(al.created_at) <= :end_date";
        }
        
        if ($userId) {
            $sql .= " AND al.user_id = :user_id";
        }
        
        if ($module) {
            $sql .= " AND al.module = :module";
        }
        
        if ($action) {
            $sql .= " AND al.action = :action";
        }
        
        $sql .= " ORDER BY al.created_at DESC";
        
        $this->db->query($sql);
        
        if ($startDate) {
            $this->db->bind(':start_date', $startDate);
        }
        
        if ($endDate) {
            $this->db->bind(':end_date', $endDate);
        }
        
        if ($userId) {
            $this->db->bind(':user_id', $userId);
        }
        
        if ($module) {
            $this->db->bind(':module', $module);
        }
        
        if ($action) {
            $this->db->bind(':action', $action);
        }
        
        return $this->db->resultSet();
    }

    public function getLog($id) {
        $sql = "SELECT al.*, u.name as user_name 
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    public function getUsers() {
        $sql = "SELECT DISTINCT u.id, u.name 
                FROM audit_logs al
                JOIN users u ON al.user_id = u.id
                ORDER BY u.name";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getModules() {
        $sql = "SELECT DISTINCT module FROM audit_logs ORDER BY module";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getActions() {
        $sql = "SELECT DISTINCT action FROM audit_logs ORDER BY action";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function clearLogs($cutoffDate) {
        $sql = "DELETE FROM audit_logs WHERE DATE(created_at) < :cutoff_date";
        $this->db->query($sql);
        $this->db->bind(':cutoff_date', $cutoffDate);
        $this->db->execute();
        
        return $this->db->rowCount();
    }
} 