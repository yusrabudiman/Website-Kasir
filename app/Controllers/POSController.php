<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\StoreSetting;
use Ramsey\Uuid\Uuid;

class POSController extends Controller {
    private $productModel;
    private $orderModel;
    private $settingModel;

    public function __construct() {
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->settingModel = $this->model('StoreSetting');
    }

    public function index() {
        $this->checkAuth(['cashier', 'admin']);
        
        // Perbaiki cara mengambil tax_rate
        $taxRate = $this->settingModel->getTaxRate() ?? 0;
        
        return $this->view('pos/index', [
            'title' => 'Point of Sale',
            'tax_rate' => $taxRate,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function searchProduct() {
        if (!$this->isPost()) {
            return $this->response(['error' => 'Invalid request method'], 405);
        }

        try {
            $search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
            
            // Debug log
            error_log("Search request received: " . $search);
            
            if (empty($search)) {
                // If search is empty, get all available products
                $products = $this->productModel->getAllAvailable();
            } else {
                $products = $this->productModel->searchAvailable($search);
            }
            
            // Debug log
            error_log("Products found: " . json_encode($products));
            
            return $this->response([
                'success' => true,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            error_log("Error in searchProduct: " . $e->getMessage());
            return $this->response([
                'success' => false,
                'error' => 'Error searching products: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProduct($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            return $this->response(['error' => 'Product not found'], 404);
        }
        return $this->response(['product' => $product]);
    }

    public function createOrder() {
        if (!$this->isPost()) {
            error_log("Invalid request method. Expected POST.");
            return $this->response(['error' => 'Invalid request method'], 405);
        }

        // Get CSRF token from POST data
        $csrfToken = $_POST['csrf_token'] ?? null;
        
        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            error_log("CSRF Token validation failed. Expected: " . $_SESSION['csrf_token'] . ", Received: " . $csrfToken);
            return $this->response(['error' => 'Invalid CSRF token'], 403);
        }

        try {
            // Debug log all POST data
            error_log("Received POST data: " . print_r($_POST, true));
            error_log("Session data: " . print_r($_SESSION, true));

            // Validate items data
            if (!isset($_POST['items'])) {
                error_log("Items data is missing in POST");
                return $this->response(['error' => 'Items data is missing'], 400);
            }

            $items = json_decode($_POST['items'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error: " . json_last_error_msg());
                error_log("Raw items data: " . $_POST['items']);
                return $this->response(['error' => 'Invalid JSON data'], 400);
            }

            if (!$items || !is_array($items) || empty($items)) {
                error_log("Invalid items array: " . print_r($items, true));
                return $this->response(['error' => 'Invalid items data'], 400);
            }

            // Validate each item
            foreach ($items as $index => $item) {
                error_log("Validating item " . $index . ": " . print_r($item, true));
                
                if (!isset($item['product_id']) || empty($item['product_id'])) {
                    error_log("Missing product_id in item " . $index);
                    return $this->response(['error' => 'Missing product_id in item ' . $index], 400);
                }
                if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                    error_log("Invalid quantity in item " . $index . ": " . ($item['quantity'] ?? 'not set'));
                    return $this->response(['error' => 'Invalid quantity in item ' . $index], 400);
                }
                if (!isset($item['price']) || !is_numeric($item['price']) || $item['price'] < 0) {
                    error_log("Invalid price in item " . $index . ": " . ($item['price'] ?? 'not set'));
                    return $this->response(['error' => 'Invalid price in item ' . $index], 400);
                }
            }

            // Validate required fields
            $requiredFields = [
                'total_amount' => 'Total amount',
                'tax_amount' => 'Tax amount',
                'final_amount' => 'Final amount',
                'payment_amount' => 'Payment amount',
                'change_amount' => 'Change amount'
            ];

            foreach ($requiredFields as $field => $label) {
                error_log("Validating field " . $field . ": " . ($_POST[$field] ?? 'not set'));
                
                if (!isset($_POST[$field])) {
                    error_log("Missing required field: " . $field);
                    return $this->response(['error' => 'Missing ' . $label], 400);
                }
                if (!is_numeric($_POST[$field])) {
                    error_log("Invalid " . $field . ": " . $_POST[$field]);
                    return $this->response(['error' => 'Invalid ' . $label], 400);
                }
            }

            // Calculate expected total from items
            $expectedTotal = 0;
            foreach ($items as $item) {
                $expectedTotal += floatval($item['price']) * intval($item['quantity']);
            }

            // Compare totals with a small tolerance for floating point arithmetic
            $tolerance = 0.01;
            $postedTotal = floatval($_POST['total_amount']);
            if (abs($expectedTotal - $postedTotal) > $tolerance) {
                error_log("Total amount mismatch. Expected: " . $expectedTotal . ", Received: " . $postedTotal);
                return $this->response(['error' => 'Total amount does not match items total'], 400);
            }

            // Validate tax and final amounts
            $postedTax = floatval($_POST['tax_amount']);
            $postedFinal = floatval($_POST['final_amount']);
            $expectedFinal = $postedTotal + $postedTax;
            
            if (abs($expectedFinal - $postedFinal) > $tolerance) {
                error_log("Final amount mismatch. Expected: " . $expectedFinal . ", Received: " . $postedFinal);
                return $this->response(['error' => 'Final amount does not match total + tax'], 400);
            }

            // Validate payment and change
            $postedPayment = floatval($_POST['payment_amount']);
            $postedChange = floatval($_POST['change_amount']);
            $expectedChange = $postedPayment - $postedFinal;
            
            if (abs($expectedChange - $postedChange) > $tolerance) {
                error_log("Change amount mismatch. Expected: " . $expectedChange . ", Received: " . $postedChange);
                return $this->response(['error' => 'Change amount does not match payment - final amount'], 400);
            }

            // Generate invoice number
            $date = date('Ymd');
            $lastInvoice = $this->orderModel->getLastInvoiceNumber($date);
            $sequence = $lastInvoice ? (intval(substr($lastInvoice, -4)) + 1) : 1;
            $invoiceNumber = sprintf("INV/%s/%04d", $date, $sequence);

            // Prepare order data
            $orderData = [
                'id' => Uuid::uuid4()->toString(),
                'invoice_number' => $invoiceNumber,
                'created_by' => $_SESSION['user_id'],
                'items' => $items,
                'total_amount' => floatval($_POST['total_amount']),
                'tax_amount' => floatval($_POST['tax_amount']),
                'final_amount' => floatval($_POST['final_amount']),
                'payment_amount' => floatval($_POST['payment_amount']),
                'change_amount' => floatval($_POST['change_amount']),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Debug log
            error_log("Prepared order data: " . print_r($orderData, true));

            // Create the order
            $this->orderModel->create($orderData);

            return $this->response([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $orderData['id'],
                'invoice_number' => $invoiceNumber
            ]);
        } catch (\Exception $e) {
            error_log("Error creating order: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->response([
                'error' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printReceipt($orderId) {
        try {
            // Debug log
            error_log("Attempting to print receipt for order: " . $orderId);
            
            // Get order data
            $order = $this->orderModel->getOrderWithItems($orderId);
            if (!$order) {
                error_log("Order not found: " . $orderId);
                return $this->response(['error' => 'Order not found'], 404);
            }

            // Get store settings
            $settings = $this->settingModel->get();
            
            // Debug log
            error_log("Order data: " . print_r($order, true));
            error_log("Settings data: " . print_r($settings, true));

            // Render receipt view
            return $this->view('pos/receipt', [
                'order' => $order,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            error_log("Error in printReceipt: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->response(['error' => 'Failed to generate receipt'], 500);
        }
    }
}
