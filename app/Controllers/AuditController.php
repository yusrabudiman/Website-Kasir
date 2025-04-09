<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AuditLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        // Limit logs to 3 rows
        $logs = array_slice($logs, 0, 3);
        
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
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Date & Time');
        $sheet->setCellValue('C1', 'User');
        $sheet->setCellValue('D1', 'Module');
        $sheet->setCellValue('E1', 'Action');
        $sheet->setCellValue('F1', 'IP Address');
        $sheet->setCellValue('G1', 'Details');
        
        // Style the header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
        
        // Add data
        $row = 2;
        foreach ($logs as $log) {
            $sheet->setCellValue('A' . $row, $log->id);
            $sheet->setCellValue('B' . $row, $log->created_at);
            $sheet->setCellValue('C' . $row, $log->user_name);
            $sheet->setCellValue('D' . $row, $log->module);
            $sheet->setCellValue('E' . $row, $log->action);
            $sheet->setCellValue('F' . $row, $log->ip_address);
            $sheet->setCellValue('G' . $row, $log->details);
            $row++;
        }
        
        // Auto-size columns
        foreach(range('A','G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set headers for Excel download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="audit_log.xlsx"');
        header('Cache-Control: max-age=0');
        
        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
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