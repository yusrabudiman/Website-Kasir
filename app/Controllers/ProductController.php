<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use Ramsey\Uuid\Uuid;

class ProductController extends Controller {
    private $productModel;

    public function __construct() {
        $this->productModel = $this->model('Product');
    }

    public function index() {
        $products = $this->productModel->getAll();
        return $this->view('products/index', [
            'title' => 'Products',
            'products' => $products,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function create() {
        if ($this->isPost()) {
            $this->validateCSRF();
            
            $data = [
                'id' => Uuid::uuid4()->toString(),
                'code' => filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING),
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
                'price' => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'stock' => filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT)
            ];

            // Validate required fields
            if (empty($data['code']) || empty($data['name']) || empty($data['price'])) {
                $_SESSION['flash_message'] = 'All required fields must be filled';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/products/create');
            }

            // Check if product code already exists
            if ($this->productModel->findByCode($data['code'])) {
                $_SESSION['flash_message'] = 'Product code already exists';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/products/create');
            }

            try {
                $this->productModel->create($data);
                $_SESSION['flash_message'] = 'Product created successfully';
                $_SESSION['flash_type'] = 'success';
                return $this->redirect('/products');
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Error creating product';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/products/create');
            }
        }

        return $this->view('products/create', [
            'title' => 'Create Product',
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function edit($id) {
        $product = $this->productModel->findById($id);
        
        if (!$product) {
            $_SESSION['flash_message'] = 'Product not found';
            $_SESSION['flash_type'] = 'error';
            return $this->redirect('/products');
        }

        if ($this->isPost()) {
            $this->validateCSRF();
            
            $data = [
                'id' => $id,
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
                'price' => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'stock' => filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT)
            ];

            // Validate required fields
            if (empty($data['name']) || empty($data['price'])) {
                $_SESSION['flash_message'] = 'All required fields must be filled';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect("/products/edit/{$id}");
            }

            try {
                $this->productModel->update($data);
                $_SESSION['flash_message'] = 'Product updated successfully';
                $_SESSION['flash_type'] = 'success';
                return $this->redirect('/products');
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Error updating product';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect("/products/edit/{$id}");
            }
        }

        return $this->view('products/edit', [
            'title' => 'Edit Product',
            'product' => $product,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function delete($id) {
        if ($this->isPost()) {
            $this->validateCSRF();
            
            try {
                $this->productModel->delete($id);
                $_SESSION['flash_message'] = 'Product deleted successfully';
                $_SESSION['flash_type'] = 'success';
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Error deleting product';
                $_SESSION['flash_type'] = 'error';
            }
        }
        
        return $this->redirect('/products');
    }

    public function search() {
        // Prevent any output before JSON response
        ob_clean();
        
        header('Content-Type: application/json');
        
        if (!$this->isPost()) {
            return $this->response(['success' => false, 'error' => 'Invalid request method'], 405);
        }

        try {
            $this->validateCSRF();
            
            $search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
            
            // Debug log
            error_log("Search request received - Term: " . $search);
            
            if (empty($search)) {
                $products = $this->productModel->getAll();
            } else {
                $products = $this->productModel->search($search);
            }
            
            $response = [
                'success' => true,
                'products' => $products
            ];
            
            // Debug log
            error_log("Search response: " . json_encode($response));
            
            return $this->response($response);
            
        } catch (\Exception $e) {
            error_log("Error in product search: " . $e->getMessage());
            return $this->response([
                'success' => false,
                'error' => 'Error searching products: ' . $e->getMessage()
            ], 500);
        }
    }
}
