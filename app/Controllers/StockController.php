<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\StockMutation;
use App\Models\Product;

class StockController extends Controller {
    private $stockMutation;
    private $product;

    public function __construct() {
        parent::__construct();
        $this->stockMutation = new StockMutation();
        $this->product = new Product();
    }

    public function index() {
        $this->checkAuth();
        
        $products = $this->product->getAll();
        return $this->view('stock/index', [
            'products' => $products
        ]);
    }

    public function mutations($productId = null) {
        $this->checkAuth();

        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? null;

        if ($productId) {
            $mutations = $this->stockMutation->getProductMutations($productId);
            $product = $this->product->getById($productId);
            
            return $this->view('stock/product_mutations', [
                'mutations' => $mutations,
                'product' => $product,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        }

        $mutations = $this->stockMutation->getMutationsByDate($startDate, $endDate, $type);
        return $this->view('stock/mutations', [
            'mutations' => $mutations,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type
        ]);
    }

    public function adjust() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $productId = $_POST['product_id'];
                $quantity = (int)$_POST['quantity'];
                $type = $_POST['type'];
                $notes = $_POST['notes'];

                // Validate stock change
                $stockChange = $this->stockMutation->validateStockChange(
                    $productId,
                    $quantity,
                    $type
                );

                // Create mutation record
                $this->stockMutation->create([
                    'product_id' => $productId,
                    'type' => $type,
                    'quantity' => $quantity,
                    'before_stock' => $stockChange['before_stock'],
                    'after_stock' => $stockChange['after_stock'],
                    'notes' => $notes,
                    'created_by' => $_SESSION['user_id']
                ]);

                $this->setFlash('success', 'Stock adjusted successfully');
                return $this->redirect('/stock/mutations/' . $productId);

            } catch (\Exception $e) {
                $this->setFlash('error', $e->getMessage());
                return $this->redirect('/stock');
            }
        }

        $products = $this->product->getAll();
        return $this->view('stock/adjust', [
            'products' => $products
        ]);
    }

    public function export() {
        $this->checkAuth();

        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? null;

        $mutations = $this->stockMutation->getMutationsByDate($startDate, $endDate, $type);

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="stock_mutations.xls"');
        header('Cache-Control: max-age=0');

        // Output Excel content
        echo "Product Code\tProduct Name\tType\tQuantity\tBefore Stock\tAfter Stock\tNotes\tDate\tUser\n";
        
        foreach ($mutations as $mutation) {
            echo implode("\t", [
                $mutation->product_code,
                $mutation->product_name,
                ucfirst($mutation->type),
                $mutation->quantity,
                $mutation->before_stock,
                $mutation->after_stock,
                $mutation->notes,
                $mutation->created_at,
                $mutation->created_by_name
            ]) . "\n";
        }
        exit;
    }
}
