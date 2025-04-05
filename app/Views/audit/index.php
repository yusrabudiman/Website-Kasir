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
        <h1 class="text-2xl font-semibold text-gray-900">Audit Trail</h1>
        <div class="space-x-2">
            <a href="/audit/export?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?><?php echo $user_filter ? '&user_id=' . $user_filter : ''; ?><?php echo $module_filter ? '&module=' . $module_filter : ''; ?><?php echo $action_filter ? '&action=' . $action_filter : ''; ?>" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                <i class="fas fa-file-excel mr-2"></i> Export to Excel
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mb-4 px-4 py-3 rounded-md <?php echo $_SESSION['flash_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
            <?php echo $_SESSION['flash_message']; ?>
        </div>
    <?php endif; ?>

    <!-- Filters Section -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6 p-4">
        <form action="/audit" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Date Range -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- User Filter -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
                <select id="user_id" name="user_id" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Users</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user->id; ?>" <?php echo $user_filter === $user->id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Module Filter -->
            <div>
                <label for="module" class="block text-sm font-medium text-gray-700">Module</label>
                <select id="module" name="module" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Modules</option>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module->module; ?>" <?php echo $module_filter === $module->module ? 'selected' : ''; ?>>
                            <?php echo ucfirst(htmlspecialchars($module->module)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                <select id="action" name="action" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Actions</option>
                    <?php foreach ($actions as $action): ?>
                        <option value="<?php echo $action->action; ?>" <?php echo $action_filter === $action->action ? 'selected' : ''; ?>>
                            <?php echo ucfirst(htmlspecialchars($action->action)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-5 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No audit logs found.</td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($log->user_name): ?>
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($log->user_name); ?>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-500">System</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo ucfirst(htmlspecialchars($log->module)); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo ucfirst(htmlspecialchars($log->action)); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($log->ip_address); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/audit/details/<?php echo $log->id; ?>" class="text-indigo-600 hover:text-indigo-900">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Clear Logs Section -->
    <div class="mt-8 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium mb-4">Clear Old Audit Logs</h2>
            <form action="/audit/clear" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="max-w-xs">
                    <label for="days_to_keep" class="block text-sm font-medium text-gray-700">Keep logs from last</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="number" id="days_to_keep" name="days_to_keep" min="7" value="30"
                               class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="inline-flex items-center px-3 py-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500">
                            days
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Minimum: 7 days</p>
                </div>
                
                <div>
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to clear old audit logs? This action cannot be undone.')"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash-alt mr-2"></i> Clear Old Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 