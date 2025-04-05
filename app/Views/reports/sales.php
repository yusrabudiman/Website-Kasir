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
        <h1 class="text-2xl font-semibold text-gray-900">Sales Report</h1>
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
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" id="start_date" name="start_date" 
                       value="<?php echo $start_date; ?>"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" id="end_date" name="end_date" 
                       value="<?php echo $end_date; ?>"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="group_by" class="block text-sm font-medium text-gray-700">Group By</label>
                <select id="group_by" name="group_by"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                    <option value="daily" <?php echo $group_by === 'daily' ? 'selected' : ''; ?>>Daily</option>
                    <option value="weekly" <?php echo $group_by === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                    <option value="monthly" <?php echo $group_by === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
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
            <dt class="text-sm font-medium text-gray-500">Total Sales</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">Rp <?php echo number_format($summary->total_sales); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Total Orders</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900"><?php echo number_format($summary->total_orders); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Average Order Value</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">Rp <?php echo number_format($summary->average_order); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Total Items Sold</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900"><?php echo number_format($summary->total_items); ?></dd>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Trend</h3>
        <canvas id="salesChart" height="300"></canvas>
    </div>

    <!-- Top Products and Sales History -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Products -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Top Selling Products</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <?php foreach ($topProducts as $product): ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo $product->quantity_sold; ?> units sold</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">Rp <?php echo number_format($product->total_sales); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sales History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Orders</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Items Sold</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($report as $row): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $row->date; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <?php echo number_format($row->orders); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <?php echo number_format($row->items_sold); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                    Rp <?php echo number_format($row->total_sales); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare chart data
    const salesData = <?php echo json_encode($report); ?>;
    const labels = salesData.map(item => item.date);
    const sales = salesData.map(item => item.total_sales);
    const orders = salesData.map(item => item.orders);

    // Create chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (Rp)',
                data: sales,
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true
            }, {
                label: 'Orders',
                data: orders,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                yAxisID: 'orders'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sales (Rp)'
                    }
                },
                orders: {
                    beginAtZero: true,
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Orders'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Handle form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const groupBy = document.getElementById('group_by').value;
        
        window.location.href = `/reports/sales?start_date=${startDate}&end_date=${endDate}&group_by=${groupBy}`;
    });
});

// Export function
function exportReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const groupBy = document.getElementById('group_by').value;
    
    window.location.href = `/reports/sales?start_date=${startDate}&end_date=${endDate}&group_by=${groupBy}&export=1`;
}
</script>
</body>
</html>
