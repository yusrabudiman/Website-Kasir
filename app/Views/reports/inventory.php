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
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Inventory Report</h1>
        <div class="space-x-2">
            <button onclick="exportReport()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-file-excel mr-2"></i> Export to Excel
            </button>
            <a href="/reports" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <form id="filterForm" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="stock_status" class="block text-sm font-medium text-gray-700">Stock Status</label>
                <select id="stock_status" name="stock_status"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                    <option value="all" <?php echo $stock_status === 'all' ? 'selected' : ''; ?>>All Items</option>
                    <option value="in_stock" <?php echo $stock_status === 'in_stock' ? 'selected' : ''; ?>>In Stock</option>
                    <option value="low_stock" <?php echo $stock_status === 'low_stock' ? 'selected' : ''; ?>>Low Stock</option>
                    <option value="out_of_stock" <?php echo $stock_status === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="sort_by" class="block text-sm font-medium text-gray-700">Sort By</label>
                <select id="sort_by" name="sort_by"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                    <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Product Name</option>
                    <option value="stock" <?php echo $sort_by === 'stock' ? 'selected' : ''; ?>>Stock Level</option>
                    <option value="value" <?php echo $sort_by === 'value' ? 'selected' : ''; ?>>Stock Value</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
                <select id="order" name="order"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                    <option value="asc" <?php echo $order === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?php echo $order === 'desc' ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Total Products</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900"><?php echo number_format($summary->total_products); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Total Stock Value</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900"><?php echo $currencySymbol; ?> <?php echo number_format($summary->total_value); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Low Stock Items</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900"><?php echo number_format($summary->low_stock); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Out of Stock</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900"><?php echo number_format($summary->out_of_stock); ?></dd>
        </div>
    </div>

    <!-- Stock Distribution Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Distribution</h3>
        <canvas id="stockChart" height="300"></canvas>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Min Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($report as $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($item->code); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item->name); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <?php echo number_format($item->stock); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <?php echo number_format($item->min_stock); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <?php echo $currencySymbol; ?>   <?php echo number_format($item->price); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            <?php echo $currencySymbol; ?>   <?php echo number_format($item->stock * $item->price); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusText = 'In Stock';
                            
                            if ($item->stock <= 0) {
                                $statusClass = 'bg-red-100 text-red-800';
                                $statusText = 'Out of Stock';
                            } elseif ($item->stock <= $item->min_stock) {
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                $statusText = 'Low Stock';
                            }
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                <?php echo $statusText; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare chart data
    const stockData = {
        inStock: <?php echo $summary->in_stock ?? 0; ?>,
        lowStock: <?php echo $summary->low_stock ?? 0; ?>,
        outOfStock: <?php echo $summary->out_of_stock ?? 0; ?>
    };

    // Create chart
    const ctx = document.getElementById('stockChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: [stockData.inStock, stockData.lowStock, stockData.outOfStock],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',  // Green
                    'rgba(245, 158, 11, 0.8)',   // Yellow
                    'rgba(239, 68, 68, 0.8)'     // Red
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(this));
        window.location.href = `/reports/inventory?${params.toString()}`;
    });
});

// Export function
function exportReport() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.append('export', '1');
    window.location.href = `/reports/inventory?${params.toString()}`;
}
</script>
</body>
</html>
