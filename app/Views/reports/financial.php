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


<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Financial Report</h1>
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
            <dt class="text-sm font-medium text-gray-500">Gross Sales</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">Rp <?php echo number_format($summary->gross_sales); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Tax Amount</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">Rp <?php echo number_format($summary->tax_amount); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Net Sales</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">Rp <?php echo number_format($summary->net_sales); ?></dd>
        </div>
        <div class="bg-white rounded-lg shadow-sm px-4 py-5">
            <dt class="text-sm font-medium text-gray-500">Average Daily Sales</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">Rp <?php echo number_format($summary->average_daily); ?></dd>
        </div>
    </div>

    <!-- Financial Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Sales Trend -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Trend</h3>
            <canvas id="salesChart" height="300"></canvas>
        </div>

        <!-- Revenue Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Distribution</h3>
            <canvas id="revenueChart" height="300"></canvas>
        </div>
    </div>

    <!-- Financial Data Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Sales</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Sales</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($sales_data as $row): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $row->date; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <?php echo number_format($row->orders); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            Rp <?php echo number_format($row->gross_sales); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            Rp <?php echo number_format($row->tax_amount); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            Rp <?php echo number_format($row->net_sales); ?>
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
    // Sales Trend Chart
    const salesData = <?php echo json_encode($sales_data); ?>;
    const labels = salesData.map(item => item.date);
    const grossSales = salesData.map(item => item.gross_sales);
    const netSales = salesData.map(item => item.net_sales);

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Gross Sales',
                data: grossSales,
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true
            }, {
                label: 'Net Sales',
                data: netSales,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true
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
                    title: {
                        display: true,
                        text: 'Amount (Rp)'
                    }
                }
            }
        }
    });

    // Revenue Distribution Chart
    const summary = {
        netSales: <?php echo $summary->net_sales; ?>,
        taxAmount: <?php echo $summary->tax_amount; ?>
    };

    const ctx2 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Net Sales', 'Tax'],
            datasets: [{
                data: [summary.netSales, summary.taxAmount],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',  // Green
                    'rgba(79, 70, 229, 0.8)'    // Indigo
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
});

// Form submission
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    window.location.href = `/reports/financial?${params.toString()}`;
});

// Export function
function exportReport() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.append('export', '1');
    window.location.href = `/reports/financial?${params.toString()}`;
}
</script>
