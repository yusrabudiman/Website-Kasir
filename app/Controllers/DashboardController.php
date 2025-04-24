<?php
namespace App\Controllers;

use App\Core\Controller; //for controller
use App\Models\Order; //for daily sales, mtd sales, ytd sales
use App\Models\Product; //for top selling products
use App\Models\StoreSetting; //for currency symbol

class DashboardController extends Controller {
    private $orderModel; 
    private $productModel;
    private $settingModel;

    public function __construct() {
        $this->orderModel = $this->model('Order');
        $this->productModel = $this->model('Product');
        $this->settingModel = $this->model('StoreSetting');
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

        // Get currency symbol
        $currencySymbol = $this->settingModel->getCurrencySymbol();

        return $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'dailySales' => $dailySales,
            'mtdSales' => $mtdSales,
            'ytdSales' => $ytdSales,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'currencySymbol' => $currencySymbol
        ]);
    }
}
