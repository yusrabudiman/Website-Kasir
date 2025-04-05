<?php
// Start the session at the very beginning, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if Composer dependencies are installed
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die(
        '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">' .
        '<h3 style="margin-top: 0;">🚫 Composer Dependencies Not Installed</h3>' .
        '<p>Please follow these steps to install the required dependencies:</p>' .
        '<ol>' .
        '<li>Download and install Composer from <a href="https://getcomposer.org/download/" style="color: #721c24;">https://getcomposer.org/download/</a></li>' .
        '<li>Open a command prompt</li>' .
        '<li>Navigate to the project directory:<br>' .
        '<code style="background: #fff3f4; padding: 2px 5px;">cd ' . __DIR__ . '</code></li>' .
        '<li>Run the following command:<br>' .
        '<code style="background: #fff3f4; padding: 2px 5px;">composer install</code></li>' .
        '</ol>' .
        '<p>After completing these steps, refresh this page.</p>' .
        '</div>'
    );
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Router.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize application configuration
App\Config\App::init();

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? '1');

// Initialize router
$router = new \App\Core\Router();

// Define routes
$router->add('GET', '', 'App\Controllers\HomeController', 'index');
$router->add('GET', 'home', 'App\Controllers\HomeController', 'index');

// Auth routes
$router->add('GET', 'login', '\App\Controllers\AuthController', 'login');
$router->add('POST', 'login', '\App\Controllers\AuthController', 'login');
$router->add('GET', 'signup', '\App\Controllers\AuthController', 'signup');
$router->add('POST', 'signup', '\App\Controllers\AuthController', 'signup');
$router->add('GET', 'logout', '\App\Controllers\AuthController', 'logout');

// Protected routes (require authentication)
// Dashboard routes
$router->add('GET', 'dashboard', '\App\Controllers\DashboardController', 'index', ['\App\Core\AuthMiddleware']);

// Products routes
$router->add('GET', 'products', '\App\Controllers\ProductController', 'index', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'products/create', '\App\Controllers\ProductController', 'create', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'products/create', '\App\Controllers\ProductController', 'create', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'products/edit/{id}', '\App\Controllers\ProductController', 'edit', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'products/edit/{id}', '\App\Controllers\ProductController', 'edit', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'products/delete/{id}', '\App\Controllers\ProductController', 'delete', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'products/search', '\App\Controllers\ProductController', 'search', ['\App\Core\AuthMiddleware']);

// POS routes
$router->add('GET', 'pos', '\App\Controllers\POSController', 'index', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'pos/search-product', '\App\Controllers\POSController', 'searchProduct', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'pos/product/{id}', '\App\Controllers\POSController', 'getProduct', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'pos/create-order', '\App\Controllers\POSController', 'createOrder', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'pos/receipt/{id}', '\App\Controllers\POSController', 'printReceipt', ['\App\Core\AuthMiddleware']);

// Stock routes
$router->add('GET', 'stock', '\App\Controllers\StockController', 'index', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'stock/mutations', '\App\Controllers\StockController', 'mutations', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'stock/mutations/{id}', '\App\Controllers\StockController', 'mutations', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'stock/adjust', '\App\Controllers\StockController', 'adjust', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'stock/adjust', '\App\Controllers\StockController', 'adjust', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'stock/export', '\App\Controllers\StockController', 'export', ['\App\Core\AuthMiddleware']);

// Report routes
$router->add('GET', 'reports', '\App\Controllers\ReportController', 'index', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'reports/sales', '\App\Controllers\ReportController', 'sales', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'reports/inventory', '\App\Controllers\ReportController', 'inventory', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'reports/financial', '\App\Controllers\ReportController', 'financial', ['\App\Core\AuthMiddleware']);

// User management routes
$router->add('GET', 'users', '\App\Controllers\UserController', 'index', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'users/create', '\App\Controllers\UserController', 'create', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'users/create', '\App\Controllers\UserController', 'create', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'users/edit/{id}', '\App\Controllers\UserController', 'edit', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'users/edit/{id}', '\App\Controllers\UserController', 'edit', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'users/delete/{id}', '\App\Controllers\UserController', 'delete', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'profile', '\App\Controllers\UserController', 'profile', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'profile', '\App\Controllers\UserController', 'profile', ['\App\Core\AuthMiddleware']);

// Settings routes
$router->add('GET', 'settings', '\App\Controllers\SettingsController', 'index', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'settings/update', '\App\Controllers\SettingsController', 'update', ['\App\Core\AuthMiddleware']);

// Audit trail routes
$router->add('GET', 'audit', '\App\Controllers\AuditController', 'index', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'audit/details/{id}', '\App\Controllers\AuditController', 'details', ['\App\Core\AuthMiddleware']);
$router->add('GET', 'audit/export', '\App\Controllers\AuditController', 'export', ['\App\Core\AuthMiddleware']);
$router->add('POST', 'audit/clear', '\App\Controllers\AuditController', 'clear', ['\App\Core\AuthMiddleware']);

// Dispatch the request
$router->dispatch();

