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
            return $this->response(['error' => 'Invalid request method'], 405);
        }

        $this->validateCSRF();

        // Get and validate the order data
        $orderData = json_decode(file_get_contents('php://input'), true);
        
        if (!$orderData || empty($orderData['items'])) {
            return $this->response(['error' => 'Invalid order data'], 400);
        }

        try {
            // Generate invoice number (format: INV/YYYYMMDD/XXXX)
            $date = date('Ymd');
            $lastInvoice = $this->orderModel->getLastInvoiceNumber($date);
            $sequence = $lastInvoice ? (intval(substr($lastInvoice, -4)) + 1) : 1;
            $invoiceNumber = sprintf("INV/%s/%04d", $date, $sequence);

            // Prepare order data
            $orderData['id'] = Uuid::uuid4()->toString();
            $orderData['invoice_number'] = $invoiceNumber;
            $orderData['created_by'] = $_SESSION['user_id'];

            // Create items with UUIDs
            foreach ($orderData['items'] as &$item) {
                $item['id'] = Uuid::uuid4()->toString();
            }

            // Create the order
            $this->orderModel->create($orderData);

            return $this->response([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $orderData['id'],
                'invoice_number' => $invoiceNumber
            ]);
        } catch (\Exception $e) {
            return $this->response([
                'error' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printReceipt($orderId) {
        $order = $this->orderModel->getOrderWithItems($orderId);
        if (!$order) {
            return $this->response(['error' => 'Order not found'], 404);
        }

        $settings = $this->settingModel->get();
        
        return $this->view('pos/receipt', [
            'order' => $order,
            'settings' => $settings
        ]);
    }
}
