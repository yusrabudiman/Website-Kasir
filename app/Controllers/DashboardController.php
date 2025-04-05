<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller {
    private $orderModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = $this->model('Order');
        $this->productModel = $this->model('Product');
    }

    public function index() {
        // Get daily sales
        $dailySales = $this->orderModel->getDailySales();
        
        // Get MTD (Month to Date) sales
        $mtdSales = $this->orderModel->getMTDSales();
        
        // Get YTD (Year to Date) sales
        $ytdSales = $this->orderModel->getYTDSales();
        
        // Get top selling products
        $topProducts = $this->productModel->getTopSellingProducts();
        
        // Get recent orders
        $recentOrders = $this->orderModel->getRecentOrders(5);
        
        // Get low stock alerts
        $lowStockProducts = $this->productModel->getLowStockProducts();

        return $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'dailySales' => $dailySales,
            'mtdSales' => $mtdSales,
            'ytdSales' => $ytdSales,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts
        ]);
    }
}
