<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\StoreSetting;
use Ramsey\Uuid\Uuid;

class SettingsController extends Controller {
    private $settingsModel;

    public function __construct() {
        parent::__construct();
        $this->settingsModel = new StoreSetting();
    }

    public function index() {
        $this->checkAuth(['admin']);
        
        $settings = $this->settingsModel->get();
        
        return $this->view('settings/index', [
            'title' => 'Store Settings',
            'settings' => $settings,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function update() {
        $this->checkAuth(['admin']);
        
        if (!$this->isPost()) {
            return $this->redirect('/settings');
        }

        $this->validateCSRF();
        
        // Collect form data
        $data = [
            'store_name' => filter_input(INPUT_POST, 'store_name', FILTER_SANITIZE_STRING),
            'store_address' => filter_input(INPUT_POST, 'store_address', FILTER_SANITIZE_STRING),
            'store_phone' => filter_input(INPUT_POST, 'store_phone', FILTER_SANITIZE_STRING),
            'store_email' => filter_input(INPUT_POST, 'store_email', FILTER_SANITIZE_EMAIL),
            'tax_percentage' => filter_input(INPUT_POST, 'tax_percentage', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'currency_symbol' => filter_input(INPUT_POST, 'currency_symbol', FILTER_SANITIZE_STRING),
            'receipt_footer' => filter_input(INPUT_POST, 'receipt_footer', FILTER_SANITIZE_STRING),
            'low_stock_threshold' => filter_input(INPUT_POST, 'low_stock_threshold', FILTER_SANITIZE_NUMBER_INT)
        ];

        // Validate required fields
        if (empty($data['store_name'])) {
            $_SESSION['flash_message'] = 'Store name is required';
            $_SESSION['flash_type'] = 'error';
            return $this->redirect('/settings');
        }

        // Process logo if uploaded
        if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] === UPLOAD_ERR_OK) {
            $logoUpload = $this->uploadLogo($_FILES['store_logo']);
            if ($logoUpload['success']) {
                $data['store_logo'] = $logoUpload['filename'];
            } else {
                $_SESSION['flash_message'] = 'Error uploading logo: ' . $logoUpload['message'];
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/settings');
            }
        }

        try {
            $this->settingsModel->save($data);
            $this->logActivity('Store settings updated');
            
            $_SESSION['flash_message'] = 'Settings updated successfully';
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = 'Error updating settings: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }

        return $this->redirect('/settings');
    }

    private function uploadLogo($file) {
        $targetDir = 'public/uploads/';
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'logo_' . Uuid::uuid4()->toString() . '.' . $fileExtension;
        $targetFile = $targetDir . $filename;

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Only JPG, PNG, and GIF files are allowed'
            ];
        }

        // Check file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'File size exceeds 2MB limit'
            ];
        }

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return [
                'success' => true,
                'filename' => $filename
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to upload file'
            ];
        }
    }

    private function logActivity($activity) {
        // Log user activity for audit trail
        if (method_exists($this, 'createAuditLog')) {
            $this->createAuditLog('settings', $activity);
        }
    }
} 