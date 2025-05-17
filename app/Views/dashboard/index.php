<?php
$formattedDailySales = number_format($dailySales->total_sales, 0, ',', '.');
$formattedMTDSales = number_format($mtdSales->total_sales, 0, ',', '.');
$formattedYTDSales = number_format($ytdSales->total_sales, 0, ',', '.');
?>

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
    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-30 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-indigo-600 text-xl font-bold"><?php echo $storeName ?? 'POS System'; ?></span>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="dashboard" class="px-3 py-2 rounded-md text-sm font-medium text-indigo-600 bg-indigo-50">Dashboard</a>
                    
                    <a href="pos" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                        Point of Sale
                    </a>
                    
                    <?php if($_SESSION['user_role'] === 'admin'): ?>
                        <a href="products" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                            Products
                        </a>
                        
                        <a href="users" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                            Users
                        </a>
                        
                        <a href="reports" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                            Reports
                        </a>
                        
                        <div class="relative inline-block text-left">
                            <button type="button" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none" id="menu-button" aria-expanded="false" aria-haspopup="true" onclick="toggleDropdown()">
                                More
                                <svg class="-mr-1 ml-1 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" id="dropdown-menu" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                <div class="py-1" role="none">
                                    <a href="settings" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
                                        Settings
                                    </a>
                                    <a href="audit" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
                                        Audit Trail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- User Profile Section -->
                <div class="flex items-center">
                    <div class="relative inline-block text-left">
                        <button type="button" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true" onclick="toggleUserMenu()">
                            <span class="mr-2"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                                <?php echo substr($_SESSION['user_name'], 0, 1); ?>
                            </div>
                        </button>
                        
                        <div class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" id="user-menu" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <div class="py-1" role="none">
                                <a href="profile" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
                                    Your Profile
                                </a>
                                <a href="logout" class="text-gray-700 hover:bg-red-50 hover:text-red-600 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
                                    Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none" aria-controls="mobile-menu" aria-expanded="false" onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="dashboard" class="bg-indigo-50 text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-base font-medium">Dashboard</a>
                <a href="pos" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Point of Sale</a>
                
                <?php if($_SESSION['user_role'] === 'admin'): ?>
                    <a href="products" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Products</a>
                    <a href="users" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Users</a>
                    <a href="settings" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Settings</a>
                    <a href="reports" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Reports</a>
                    <a href="audit" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Audit Trail</a>
                <?php endif; ?>
                
                <div class="border-t border-gray-200 pt-2">
                    <a href="profile" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Your Profile</a>
                    <a href="logout" class="text-gray-600 hover:bg-gray-50 hover:text-red-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Sign out</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-16 pb-6 px-4 sm:px-6 lg:px-8 min-h-screen">
        <!-- Dashboard Header with gradient background -->
        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg mb-8">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 px-6 py-8 text-white">
                <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                <p class="mt-2 text-indigo-100">Welcome back, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>!</p>
                <div class="mt-4 inline-flex space-x-2">
                    <span class="px-2.5 py-1 text-xs rounded-full bg-indigo-200 text-indigo-800 font-medium">
                        <?php echo date('d M Y'); ?>
                    </span>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-purple-200 text-purple-800 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <?php echo date('H:i'); ?>
                    </span>
                </div>
            </div>
            <div class="absolute bottom-0 right-0 transform translate-y-1/4 -mr-16">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-48 w-48 text-indigo-300 opacity-20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <!-- Sales Summary Cards with hover effects and animations -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Daily Sales -->
            <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
            <div class="p-6">
                <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full p-3 shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-gray-900">Daily Sales</h3>
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">Today</span>
                            </div>
                            <div class="mt-2 flex flex-col">
                                <div class="text-2xl font-bold text-gray-900"><?php echo $currencySymbol; ?> <?php echo $formattedDailySales; ?></div>
                                <div class="text-sm text-gray-600 flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <?php echo $dailySales->total_orders; ?> orders
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MTD Sales -->
            <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-green-400 to-green-600 rounded-full p-3 shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-gray-900">Month to Date Sales</h3>
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 font-medium">MTD</span>
                            </div>
                            <div class="mt-2 flex flex-col">
                                <div class="text-2xl font-bold text-gray-900"><?php echo $currencySymbol; ?> <?php echo $formattedMTDSales; ?></div>
                                <div class="text-sm text-gray-600 flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <?php echo $mtdSales->total_orders; ?> orders
                                    </span>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <!-- YTD Sales -->
            <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
            <div class="p-6">
                <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full p-3 shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-gray-900">Year to Date Sales</h3>
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800 font-medium">YTD</span>
                            </div>
                            <div class="mt-2 flex flex-col">
                                <div class="text-2xl font-bold text-gray-900"><?php echo $currencySymbol; ?> <?php echo $formattedYTDSales; ?></div>
                                <div class="text-sm text-gray-600 flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <?php echo $ytdSales->total_orders; ?> orders
                                    </span>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Selling Products -->
            <div class="bg-white shadow-md rounded-xl border border-gray-100 transition-all duration-300 hover:shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Top Selling Products
                    </h3>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">Best Sellers</span>
                </div>
            <div class="p-6">
                    <?php if (empty($topProducts)): ?>
                        <div class="text-center py-6">
                            <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076478.png" alt="No Data" class="w-12 h-12 opacity-40">
                            </div>
                            <p class="text-gray-500 mb-1">No products data available</p>
                            <p class="text-xs text-gray-400">Products will appear here once sales are made</p>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($topProducts as $product): ?>
                            <li class="py-4 hover:bg-gray-50 rounded-lg transition-colors duration-150 -mx-2 px-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-gradient-to-br from-gray-100 to-blue-50 rounded-lg p-2.5 mr-3 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($product->name); ?></p>
                                            <p class="text-xs text-gray-500 mt-0.5">Code: <span class="font-mono bg-gray-100 px-1 py-0.5 rounded"><?php echo htmlspecialchars($product->code); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="bg-blue-50 text-blue-700 py-1.5 px-3 rounded-full text-xs font-medium border border-blue-100">
                                        <?php echo $product->total_quantity_sold; ?> sold
                                        </span>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
            </div>
        </div>

        <!-- Recent Orders -->
            <div class="bg-white shadow-md rounded-xl border border-gray-100 transition-all duration-300 hover:shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Recent Orders
                    </h3>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">Latest Transactions</span>
                </div>
            <div class="p-6">
                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center py-6">
                            <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <img src="https://cdn-icons-png.flaticon.com/512/1046/1046857.png" alt="No Orders" class="w-12 h-12 opacity-40">
                            </div>
                            <p class="text-gray-500 mb-1">No recent orders available</p>
                            <p class="text-xs text-gray-400">Orders will appear here as they are processed</p>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($recentOrders as $order): ?>
                            <li class="py-4 hover:bg-gray-50 rounded-lg transition-colors duration-150 -mx-2 px-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-gradient-to-br from-gray-100 to-green-50 rounded-lg p-2.5 mr-3 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Invoice: <span class="font-mono"><?php echo htmlspecialchars($order->invoice_number); ?></span></p>
                                            <p class="text-xs text-gray-500 mt-0.5">By: <?php echo htmlspecialchars($order->cashier_name); ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900"><?php echo $currencySymbol; ?> <?php echo number_format($order->final_amount, 0, ',', '.'); ?></p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <?php echo $order->total_items; ?> items
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <?php if (!empty($lowStockProducts)): ?>
        <div class="mt-6 bg-white shadow-md rounded-xl border border-gray-100 transition-all duration-300 hover:shadow-xl overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-orange-50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Low Stock Alert
                </h3>
                <span class="px-2.5 py-1 text-xs rounded-full bg-red-100 text-red-800 font-medium">Needs Attention</span>
            </div>
        <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Code</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Price</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($lowStockProducts as $product): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono"><?php echo htmlspecialchars($product->code); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($product->name); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <span class="inline-flex items-center <?php echo $product->stock <= 5 ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200'; ?> py-1 px-2.5 rounded-full text-xs font-medium">
                                        <?php echo $product->stock <= 5 ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>' : ''; ?>
                                        <?php echo $product->stock; ?> units
                                    </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                        <?php echo $currencySymbol; ?> <?php echo number_format($product->price, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add custom style for background pattern -->
    <style>
    .bg-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    </style>

    <!-- JavaScript for dropdown menus -->
    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdown-menu');
            menu.classList.toggle('hidden');
        }
        
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }
        
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
        
        // Close the dropdown menus when clicking outside
        window.addEventListener('click', function(e) {
            const dropdownMenu = document.getElementById('dropdown-menu');
            const userMenu = document.getElementById('user-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (!e.target.closest('#menu-button') && dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
                dropdownMenu.classList.add('hidden');
            }
            
            if (!e.target.closest('#user-menu-button') && userMenu && !userMenu.classList.contains('hidden')) {
                userMenu.classList.add('hidden');
            }
            
            if (!e.target.closest('button[aria-controls="mobile-menu"]') && mobileMenu && !mobileMenu.classList.contains('hidden') && window.innerWidth < 768) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
