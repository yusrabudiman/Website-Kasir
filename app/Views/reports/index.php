<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_ENV['APP_NAME']; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">

<?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-6 py-8 pt-24">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Reports Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sales Report Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-medium text-gray-900">Sales Report</h2>
                        <p class="text-sm text-gray-500">View sales performance and trends</p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="/reports/sales" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-external-link-alt mr-2"></i> View Report
                    </a>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <ul class="text-gray-600 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Sales trends analysis</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Top selling products</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Daily/monthly breakdown</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Inventory Report Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-medium text-gray-900">Inventory Report</h2>
                        <p class="text-sm text-gray-500">Track stock levels and value</p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="/reports/inventory" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-external-link-alt mr-2"></i> View Report
                    </a>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <ul class="text-gray-600 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Stock level monitoring</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Low stock alerts</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Inventory valuation</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Financial Report Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-medium text-gray-900">Financial Report</h2>
                        <p class="text-sm text-gray-500">Monitor financial performance</p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="/reports/financial" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                        <i class="fas fa-external-link-alt mr-2"></i> View Report
                    </a>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <ul class="text-gray-600 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Revenue analysis</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Tax calculations</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Daily/monthly summaries</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Today's Sales -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg px-4 py-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Today's Sales</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo isset($stats->today_sales) ? 'Rp ' . number_format($stats->today_sales) : 'Rp 0'; ?></dd>
                <dd class="mt-1 text-sm text-gray-500"><?php echo isset($stats->today_orders) ? $stats->today_orders . ' orders' : '0 orders'; ?></dd>
            </div>

            <!-- This Month -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg px-4 py-5">
                <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo isset($stats->month_sales) ? 'Rp ' . number_format($stats->month_sales) : 'Rp 0'; ?></dd>
                <dd class="mt-1 text-sm text-gray-500"><?php echo isset($stats->month_orders) ? $stats->month_orders . ' orders' : '0 orders'; ?></dd>
            </div>

            <!-- Low Stock Items -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg px-4 py-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo isset($stats->low_stock) ? $stats->low_stock : '0'; ?></dd>
                <dd class="mt-1 text-sm text-gray-500">products need attention</dd>
            </div>

            <!-- Total Products -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg px-4 py-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo isset($stats->total_products) ? $stats->total_products : '0'; ?></dd>
                <dd class="mt-1 text-sm text-gray-500">in inventory</dd>
            </div>
        </div>
    </div>
</div>
</body>
</html>
