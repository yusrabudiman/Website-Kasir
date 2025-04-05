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
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => Uuid::uuid4()->toString(),
                ':user_id' => $data['user_id'] ?? $_SESSION['user_id'] ?? null,
                ':module' => $data['module'],
                ':action' => $data['action'],
                ':details' => $data['details'] ?? '',
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
            ]);
            
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
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND DATE(al.created_at) >= :start_date";
            $params[':start_date'] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND DATE(al.created_at) <= :end_date";
            $params[':end_date'] = $endDate;
        }
        
        if ($userId) {
            $sql .= " AND al.user_id = :user_id";
            $params[':user_id'] = $userId;
        }
        
        if ($module) {
            $sql .= " AND al.module = :module";
            $params[':module'] = $module;
        }
        
        if ($action) {
            $sql .= " AND al.action = :action";
            $params[':action'] = $action;
        }
        
        $sql .= " ORDER BY al.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getLog($id) {
        $sql = "SELECT al.*, u.name as user_name 
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getUsers() {
        $sql = "SELECT DISTINCT u.id, u.name 
                FROM audit_logs al
                JOIN users u ON al.user_id = u.id
                ORDER BY u.name";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getModules() {
        $sql = "SELECT DISTINCT module FROM audit_logs ORDER BY module";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getActions() {
        $sql = "SELECT DISTINCT action FROM audit_logs ORDER BY action";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function clearLogs($cutoffDate) {
        $sql = "DELETE FROM audit_logs WHERE DATE(created_at) < :cutoff_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cutoff_date' => $cutoffDate]);
        
        return $stmt->rowCount();
    }
} 