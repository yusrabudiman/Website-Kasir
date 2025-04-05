<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AuditLog;

class AuditController extends Controller {
    private $auditModel;

    public function __construct() {
        parent::__construct();
        $this->auditModel = new AuditLog();
    }

    public function index() {
        $this->checkAuth(['admin']);
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $userFilter = $_GET['user_id'] ?? null;
        $moduleFilter = $_GET['module'] ?? null;
        $actionFilter = $_GET['action'] ?? null;
        
        $logs = $this->auditModel->getLogs($startDate, $endDate, $userFilter, $moduleFilter, $actionFilter);
        $users = $this->auditModel->getUsers();
        $modules = $this->auditModel->getModules();
        $actions = $this->auditModel->getActions();
        
        return $this->view('audit/index', [
            'title' => 'Audit Trail',
            'logs' => $logs,
            'users' => $users,
            'modules' => $modules,
            'actions' => $actions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user_filter' => $userFilter,
            'module_filter' => $moduleFilter,
            'action_filter' => $actionFilter
        ]);
    }

    public function details($id) {
        $this->checkAuth(['admin']);
        
        $log = $this->auditModel->getLog($id);
        
        if (!$log) {
            $_SESSION['flash_message'] = 'Audit log entry not found';
            $_SESSION['flash_type'] = 'error';
            return $this->redirect('/audit');
        }
        
        return $this->view('audit/details', [
            'title' => 'Audit Log Details',
            'log' => $log
        ]);
    }

    public function export() {
        $this->checkAuth(['admin']);
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $userFilter = $_GET['user_id'] ?? null;
        $moduleFilter = $_GET['module'] ?? null;
        $actionFilter = $_GET['action'] ?? null;
        
        $logs = $this->auditModel->getLogs($startDate, $endDate, $userFilter, $moduleFilter, $actionFilter);
        
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="audit_log.xls"');
        header('Cache-Control: max-age=0');
        
        // Output Excel content
        echo "ID\tDate & Time\tUser\tModule\tAction\tIP Address\tDetails\n";
        
        foreach ($logs as $log) {
            // Clean up details for export
            $details = str_replace("\n", " ", $log->details);
            $details = str_replace("\t", " ", $details);
            
            echo implode("\t", [
                $log->id,
                $log->created_at,
                $log->user_name,
                $log->module,
                $log->action,
                $log->ip_address,
                $details
            ]) . "\n";
        }
        exit;
    }

    public function clear() {
        $this->checkAuth(['admin']);
        
        if (!$this->isPost()) {
            return $this->redirect('/audit');
        }
        
        $this->validateCSRF();
        
        // Get days to keep parameter
        $daysToKeep = (int)$_POST['days_to_keep'] ?? 30;
        
        if ($daysToKeep < 7) {
            $_SESSION['flash_message'] = 'You must keep at least 7 days of audit logs';
            $_SESSION['flash_type'] = 'error';
            return $this->redirect('/audit');
        }
        
        $cutoffDate = date('Y-m-d', strtotime("-{$daysToKeep} days"));
        
        try {
            $deletedCount = $this->auditModel->clearLogs($cutoffDate);
            
            $_SESSION['flash_message'] = "Successfully cleared {$deletedCount} log entries older than {$cutoffDate}";
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = 'Error clearing audit logs: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }
        
        return $this->redirect('/audit');
    }
} 