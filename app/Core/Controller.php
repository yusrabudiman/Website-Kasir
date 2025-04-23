<?php
namespace App\Core;

use Ramsey\Uuid\Uuid;
use App\Models\AuditLog;
use App\Core\Cache;

class Controller {
    protected $cache;

    public function __construct() {
        $this->cache = Cache::getInstance();
    }

    protected function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . "/../../app/Views/{$view}.php";
    }

    protected function model($model) {
        $modelClass = 'App\\Models\\' . $model;
        if (!class_exists($modelClass)) {
            require_once __DIR__ . "/../../app/Models/{$model}.php";
        }
        return new $modelClass();
    }

    protected function generateUuid() {
        return Uuid::uuid4()->toString();
    }

    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function response($data, $statusCode = 200) {
        // Clear any previous output
        if (ob_get_length()) ob_clean();
        
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->response(['error' => 'Invalid CSRF token'], 403);
        }
    }

    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    protected function checkAuth($allowedRoles = []) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $_SESSION['flash_message'] = 'Please login to continue';
            $_SESSION['flash_type'] = 'error';
            $this->redirect('/login');
            return false;
        }
        
        // Check cache for user data
        $cachedUser = $this->cache->get('user_' . $_SESSION['user_id']);
        if ($cachedUser) {
            // Update last activity
            $cachedUser['last_activity'] = time();
            $this->cache->set('user_' . $_SESSION['user_id'], $cachedUser, 3600);
        } else {
            // If cache expired, verify from session
            $this->cache->set('user_' . $_SESSION['user_id'], [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_role'],
                'email' => $_SESSION['user_email'] ?? '',
                'last_activity' => time()
            ], 3600);
        }
        
        // Check role if specified
        if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
            $_SESSION['flash_message'] = 'You do not have permission to access this page';
            $_SESSION['flash_type'] = 'error';
            $this->redirect('/dashboard');
        }
        
        return true;
    }
    
    protected function setFlash($type, $message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    protected function createAuditLog($module, $action, $details = '') {
        try {
            $auditLog = new AuditLog();
            return $auditLog->create([
                'module' => $module,
                'action' => $action,
                'details' => $details
            ]);
        } catch (\Exception $e) {
            // Log error but don't interrupt application flow
            error_log('Failed to create audit log: ' . $e->getMessage());
            return false;
        }
    }
} 