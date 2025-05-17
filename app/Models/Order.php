<?php
namespace App\Models;

use App\Core\Database;
use Ramsey\Uuid\Uuid;

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDailySales() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(final_amount), 0) as total_sales,
                COALESCE(SUM(tax_amount), 0) as total_tax
            FROM orders 
            WHERE DATE(created_at) = CURDATE()
        ");
        return $this->db->single();
    }

    public function getMTDSales() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(final_amount), 0) as total_sales,
                COALESCE(SUM(tax_amount), 0) as total_tax
            FROM orders 
            WHERE 
                YEAR(created_at) = YEAR(CURDATE()) 
                AND MONTH(created_at) = MONTH(CURDATE())
        ");
        return $this->db->single();
    }

    public function getYTDSales() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(final_amount), 0) as total_sales,
                COALESCE(SUM(tax_amount), 0) as total_tax
            FROM orders 
            WHERE YEAR(created_at) = YEAR(CURDATE())
        ");
        return $this->db->single();
    }

    public function getRecentOrders($limit = 5) {
        $this->db->query("
            SELECT 
                o.id,
                o.invoice_number,
                o.final_amount,
                o.created_at,
                u.name as cashier_name,
                COUNT(oi.id) as total_items
            FROM orders o
            JOIN users u ON o.created_by = u.id
            JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function getSalesReport($startDate, $endDate, $groupBy = 'daily') {
        // Define the GROUP BY clause based on the grouping parameter
        switch ($groupBy) {
            case 'weekly':
                $dateFormat = "YEARWEEK(o.created_at, 1)";
                $selectDate = "CONCAT('Week ', WEEK(o.created_at, 1), ', ', YEAR(o.created_at)) as date";
                break;
            case 'monthly':
                $dateFormat = "DATE_FORMAT(o.created_at, '%Y-%m')";
                $selectDate = "DATE_FORMAT(o.created_at, '%M %Y') as date";
                break;
            default: // daily
                $dateFormat = "DATE(o.created_at)";
                $selectDate = "DATE(o.created_at) as date";
                break;
        }

        $this->db->query("
            SELECT 
                {$selectDate},
                COUNT(DISTINCT o.id) as orders,
                COALESCE(SUM(oi.quantity), 0) as items_sold,
                COALESCE(SUM(o.final_amount), 0) as total_sales
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
            GROUP BY {$dateFormat}
            ORDER BY 
                CASE 
                    WHEN '{$groupBy}' = 'weekly' THEN YEARWEEK(o.created_at, 1)
                    WHEN '{$groupBy}' = 'monthly' THEN DATE_FORMAT(o.created_at, '%Y-%m')
                    ELSE DATE(o.created_at)
                END ASC
        ");
        
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->resultSet();
    }

    public function getTopProducts($startDate, $endDate, $limit = 10) {
        $this->db->query("
            SELECT 
                p.id,
                p.name,
                p.code,
                SUM(oi.quantity) as quantity_sold,
                SUM(oi.subtotal) as total_sales
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
            GROUP BY p.id
            ORDER BY quantity_sold DESC
            LIMIT :limit
        ");
        
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    public function getSalesSummary($startDate, $endDate) {
        $this->db->query("
            SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                COALESCE(SUM(o.final_amount), 0) as total_sales,
                COALESCE(SUM(oi.quantity), 0) as total_items,
                COALESCE(SUM(o.final_amount) / COUNT(DISTINCT o.id), 0) as average_order
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
        ");
        
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->single();
    }

    public function getFinancialReport($startDate, $endDate, $groupBy = 'daily') {
        // Define the GROUP BY clause based on the grouping parameter
        switch ($groupBy) {
            case 'weekly':
                $dateFormat = "YEARWEEK(created_at, 1)";
                $selectDate = "CONCAT('Week ', WEEK(created_at, 1), ' (', DATE_FORMAT(MIN(created_at), '%d %b'), ' - ', DATE_FORMAT(MAX(created_at), '%d %b %Y'), ')') as date";
                break;
            case 'monthly':
                $dateFormat = "DATE_FORMAT(created_at, '%Y-%m')";
                $selectDate = "DATE_FORMAT(created_at, '%M %Y') as date";
                break;
            default: // daily
                $dateFormat = "DATE(created_at)";
                $selectDate = "DATE_FORMAT(created_at, '%d %b %Y') as date";
                break;
        }

        $this->db->query("
            SELECT 
                {$selectDate},
                COUNT(*) as orders,
                COALESCE(SUM(total_amount), 0) as gross_sales,
                COALESCE(SUM(tax_amount), 0) as tax_amount,
                COALESCE(SUM(final_amount), 0) as net_sales
            FROM orders
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date
            GROUP BY {$dateFormat}
            ORDER BY 
                CASE 
                    WHEN '{$groupBy}' = 'weekly' THEN YEARWEEK(created_at, 1)
                    WHEN '{$groupBy}' = 'monthly' THEN DATE_FORMAT(created_at, '%Y-%m')
                    ELSE DATE(created_at)
                END ASC
        ");
        
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->resultSet();
    }

    public function getFinancialSummary($startDate, $endDate) {
        // Hitung selisih hari terlebih dahulu untuk menghindari binding parameter yang sama berkali-kali
        $this->db->query("SELECT DATEDIFF(:end_date, :start_date) as date_diff");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        $dateDiff = $this->db->single()->date_diff;
        
        // Gunakan nilai yang sudah dihitung
        $this->db->query("
            SELECT 
                COALESCE(SUM(total_amount), 0) as gross_sales,
                COALESCE(SUM(tax_amount), 0) as tax_amount,
                COALESCE(SUM(final_amount), 0) as net_sales,
                COALESCE(SUM(final_amount) / :date_diff, 0) as average_daily
            FROM orders
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date
        ");
        
        $this->db->bind(':date_diff', $dateDiff > 0 ? $dateDiff : 1); // Hindari division by zero
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->single();
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Debug log
            error_log("Starting order creation with data: " . print_r($data, true));

            // Insert order
            $this->db->query("
                INSERT INTO orders (
                    id, invoice_number, total_amount, tax_amount, 
                    final_amount, payment_amount, change_amount, created_by, created_at
                ) VALUES (
                    :id, :invoice_number, :total_amount, :tax_amount,
                    :final_amount, :payment_amount, :change_amount, :created_by, :created_at
                )
            ");

            $this->db->bind(':id', $data['id']);
            $this->db->bind(':invoice_number', $data['invoice_number']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':tax_amount', $data['tax_amount']);
            $this->db->bind(':final_amount', $data['final_amount']);
            $this->db->bind(':payment_amount', $data['payment_amount']);
            $this->db->bind(':change_amount', $data['change_amount']);
            $this->db->bind(':created_by', $data['created_by']);
            $this->db->bind(':created_at', $data['created_at']);

            $this->db->execute();
            error_log("Order inserted successfully");

            // Insert order items
            foreach ($data['items'] as $item) {
                error_log("Processing order item: " . print_r($item, true));

                $this->db->query("
                    INSERT INTO order_items (
                        id, order_id, product_id, quantity, price, subtotal, created_at
                    ) VALUES (
                        :id, :order_id, :product_id, :quantity, :price, :subtotal, :created_at
                    )
                ");

                $this->db->bind(':id', Uuid::uuid4()->toString());
                $this->db->bind(':order_id', $data['id']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->bind(':created_at', date('Y-m-d H:i:s'));

                $this->db->execute();
                error_log("Order item inserted successfully");

                // Update product stock
                $this->db->query("
                    UPDATE products 
                    SET stock = stock - :quantity 
                    WHERE id = :product_id
                ");
                
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->execute();
                error_log("Product stock updated successfully");
            }

            $this->db->commit();
            error_log("Order creation completed successfully");
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in Order::create: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getLastInvoiceNumber($date) {
        $this->db->query("
            SELECT invoice_number 
            FROM orders 
            WHERE invoice_number LIKE :prefix 
            ORDER BY invoice_number DESC 
            LIMIT 1
        ");
        $this->db->bind(':prefix', "INV/{$date}/%");
        $result = $this->db->single();
        return $result ? $result->invoice_number : null;
    }

    public function getOrderWithItems($orderId) {
        try {
            // Get order data with cashier name
            $this->db->query("
                SELECT 
                    o.*,
                    u.name as cashier_name
                FROM orders o
                LEFT JOIN users u ON o.created_by = u.id
                WHERE o.id = :order_id
            ");
            
            $this->db->bind(':order_id', $orderId);
            $order = $this->db->single();

            if (!$order) {
                error_log("Order not found: " . $orderId);
                return null;
            }

            // Get order items with product details
            $this->db->query("
                SELECT 
                    oi.*,
                    p.name,
                    p.code,
                    p.stock
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ");
            
            $this->db->bind(':order_id', $orderId);
            $items = $this->db->resultSet();

            // Add items to order object
            $order->items = $items;

            // Get store settings for tax info
            $this->db->query("SELECT tax_rate as tax_percentage FROM store_settings LIMIT 1");
            $settings = $this->db->single();
            $order->tax_percentage = $settings ? $settings->tax_percentage : 0;

            return $order;
        } catch (\Exception $e) {
            error_log("Error in getOrderWithItems: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}
