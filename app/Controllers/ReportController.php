<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMutation;

class ReportController extends Controller {
    private $order;
    private $product;
    private $stockMutation;

    public function __construct() {
        parent::__construct();
        $this->order = new Order();
        $this->product = new Product();
        $this->stockMutation = new StockMutation();
    }

    public function index() {
        $this->checkAuth();
        return $this->view('reports/index');
    }

    public function sales() {
        $this->checkAuth();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $groupBy = $_GET['group_by'] ?? 'daily';

        $report = $this->order->getSalesReport($startDate, $endDate, $groupBy);
        $topProducts = $this->order->getTopProducts($startDate, $endDate);
        $summary = $this->order->getSalesSummary($startDate, $endDate);

        if (isset($_GET['export'])) {
            return $this->exportSalesReport($report, $topProducts, $summary, $startDate, $endDate);
        }

        return $this->view('reports/sales', [
            'report' => $report,
            'topProducts' => $topProducts,
            'summary' => $summary,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'group_by' => $groupBy
        ]);
    }

    public function inventory() {
        $this->checkAuth();
        
        $stockStatus = $_GET['stock_status'] ?? 'all';
        $sortBy = $_GET['sort_by'] ?? 'name';
        $order = $_GET['order'] ?? 'asc';

        $report = $this->product->getInventoryReport($stockStatus, $sortBy, $order);
        $summary = $this->product->getInventorySummary();

        if (isset($_GET['export'])) {
            return $this->exportInventoryReport($report, $summary);
        }

        return $this->view('reports/inventory', [
            'report' => $report,
            'summary' => $summary,
            'stock_status' => $stockStatus,
            'sort_by' => $sortBy,
            'order' => $order
        ]);
    }

    public function financial() {
        $this->checkAuth();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $groupBy = $_GET['group_by'] ?? 'daily';

        $salesData = $this->order->getFinancialReport($startDate, $endDate, $groupBy);
        $summary = $this->order->getFinancialSummary($startDate, $endDate);

        if (isset($_GET['export'])) {
            return $this->exportFinancialReport($salesData, $summary, $startDate, $endDate);
        }

        return $this->view('reports/financial', [
            'sales_data' => $salesData,
            'summary' => $summary,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'group_by' => $groupBy
        ]);
    }

    private function exportSalesReport($report, $topProducts, $summary, $startDate, $endDate) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="sales_report.xls"');
        header('Cache-Control: max-age=0');

        // Sales Summary
        echo "Sales Report ($startDate to $endDate)\n\n";
        echo "Total Sales\tTotal Orders\tAverage Order Value\tTotal Items Sold\n";
        echo implode("\t", [
            number_format($summary->total_sales),
            $summary->total_orders,
            number_format($summary->average_order),
            $summary->total_items
        ]) . "\n\n";

        // Sales by Period
        echo "Sales by Period\n";
        echo "Date\tOrders\tItems Sold\tTotal Sales\n";
        foreach ($report as $row) {
            echo implode("\t", [
                $row->date,
                $row->orders,
                $row->items_sold,
                number_format($row->total_sales)
            ]) . "\n";
        }

        // Top Products
        echo "\nTop Selling Products\n";
        echo "Product\tQuantity Sold\tTotal Sales\n";
        foreach ($topProducts as $product) {
            echo implode("\t", [
                $product->name,
                $product->quantity_sold,
                number_format($product->total_sales)
            ]) . "\n";
        }
        exit;
    }

    private function exportInventoryReport($report, $summary) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="inventory_report.xls"');
        header('Cache-Control: max-age=0');

        // Inventory Summary
        echo "Inventory Summary\n\n";
        echo "Total Products\tTotal Stock Value\tLow Stock Items\tOut of Stock Items\n";
        echo implode("\t", [
            $summary->total_products,
            number_format($summary->total_value),
            $summary->low_stock,
            $summary->out_of_stock
        ]) . "\n\n";

        // Inventory Details
        echo "Product Code\tProduct Name\tStock\tMin Stock\tPrice\tStock Value\tStatus\n";
        foreach ($report as $item) {
            $status = 'In Stock';
            if ($item->stock <= 0) {
                $status = 'Out of Stock';
            } elseif ($item->stock <= $item->min_stock) {
                $status = 'Low Stock';
            }

            echo implode("\t", [
                $item->code,
                $item->name,
                $item->stock,
                $item->min_stock,
                number_format($item->price),
                number_format($item->stock * $item->price),
                $status
            ]) . "\n";
        }
        exit;
    }

    private function exportFinancialReport($salesData, $summary, $startDate, $endDate) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="financial_report.xls"');
        header('Cache-Control: max-age=0');

        // Financial Summary
        echo "Financial Report ($startDate to $endDate)\n\n";
        echo "Gross Sales\tTax Amount\tNet Sales\tAverage Daily Sales\n";
        echo implode("\t", [
            number_format($summary->gross_sales),
            number_format($summary->tax_amount),
            number_format($summary->net_sales),
            number_format($summary->average_daily)
        ]) . "\n\n";

        // Daily Breakdown
        echo "Date\tGross Sales\tTax\tNet Sales\tOrders\n";
        foreach ($salesData as $row) {
            echo implode("\t", [
                $row->date,
                number_format($row->gross_sales),
                number_format($row->tax_amount),
                number_format($row->net_sales),
                $row->orders
            ]) . "\n";
        }
        exit;
    }
}
