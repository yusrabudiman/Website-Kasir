<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'POS System'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                    },
                },
            }
        }
    </script>
    <style>
        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-right: 3px solid white;
        }
        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        @media (max-width: 768px) {
            .sidebar {
                left: -240px;
                transition: left 0.3s;
            }
            .sidebar.open {
                left: 0;
            }
            .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex h-screen">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Top Navigation Bar -->
        <nav class="fixed top-0 left-0 right-0 bg-primary-800 text-white z-20 shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo and Toggle Button -->
                    <div class="flex items-center">
                        <button id="openSidebar" class="text-white mr-3 md:hidden">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="flex items-center">
                            <i class="fas fa-cash-register text-2xl mr-3"></i>
                            <span class="text-lg font-bold">POS System</span>
                        </div>
                    </div>
                    
                    <!-- Top Navigation Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="dashboard" class="px-3 py-2 text-sm rounded-md hover:bg-primary-700 <?php echo basename($_SERVER['REQUEST_URI']) == 'dashboard' ? 'bg-primary-700' : ''; ?>">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="pos" class="px-3 py-2 text-sm rounded-md hover:bg-primary-700 <?php echo basename($_SERVER['REQUEST_URI']) == 'pos' ? 'bg-primary-700' : ''; ?>">
                            <i class="fas fa-shopping-cart mr-2"></i> POS
                        </a>
                        <a href="stock" class="px-3 py-2 text-sm rounded-md hover:bg-primary-700 <?php echo basename($_SERVER['REQUEST_URI']) == 'stock' ? 'bg-primary-700' : ''; ?>">
                            <i class="fas fa-boxes mr-2"></i> Stock
                        </a>
                        <a href="reports" class="px-3 py-2 text-sm rounded-md hover:bg-primary-700 <?php echo basename($_SERVER['REQUEST_URI']) == 'reports' ? 'bg-primary-700' : ''; ?>">
                            <i class="fas fa-chart-bar mr-2"></i> Reports
                        </a>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <div class="relative group">
                                <button class="px-3 py-2 text-sm rounded-md hover:bg-primary-700 flex items-center">
                                    <i class="fas fa-cog mr-2"></i> Admin <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                                    <a href="products" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-box mr-2"></i> Products
                                    </a>
                                    <a href="users" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-users mr-2"></i> Users
                                    </a>
                                    <a href="settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                    <a href="audit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-history mr-2"></i> Audit Trail
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- User Profile -->
                    <div class="flex items-center">
                        <div class="relative group">
                            <button class="flex items-center hover:bg-primary-700 rounded-full py-1 px-2">
                                <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center mr-2">
                                    <span class="text-white font-semibold"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></span>
                                </div>
                                <span class="text-sm mr-1"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                                <a href="profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-cog mr-2"></i> My Profile
                                </a>
                                <a href="logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar bg-primary-800 text-white w-64 md:fixed md:h-screen fixed h-full z-10 overflow-y-auto">
            <div class="p-4 flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center">
                        <i class="fas fa-cash-register text-2xl mr-3"></i>
                        <span class="text-lg font-bold">POS System</span>
                    </div>
                    <button id="closeSidebar" class="text-white md:hidden">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- User info -->
                <div class="mb-8 p-3 bg-primary-700 rounded-lg flex items-center">
                    <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center mr-3">
                        <span class="text-white font-semibold"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                        <p class="text-xs text-gray-300"><?php echo ucfirst($_SESSION['user_role']); ?></p>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <nav class="flex-1 space-y-1">
                    <p class="text-xs text-gray-400 uppercase font-bold px-3 mb-2">Menu</p>
                    
                    <!-- Dashboard - both roles -->
                    <a href="dashboard" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt w-5 mr-3 text-center"></i>
                        Dashboard
                    </a>
                    
                    <!-- POS - both roles -->
                    <a href="pos" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'pos' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart w-5 mr-3 text-center"></i>
                        Point of Sale
                    </a>
                    
                    <!-- Stock - both roles -->
                    <a href="stock" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'stock' ? 'active' : ''; ?>">
                        <i class="fas fa-boxes w-5 mr-3 text-center"></i>
                        Stock
                    </a>
                    
                    <!-- Reports - available to both roles -->
                    <a href="reports" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'reports' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar w-5 mr-3 text-center"></i>
                        Reports
                    </a>
                    
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <!-- Admin Only Section -->
                        <p class="text-xs text-gray-400 uppercase font-bold px-3 mb-2 mt-6">Admin Management</p>
                        
                        <!-- Products -->
                        <a href="products" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'products' ? 'active' : ''; ?>">
                            <i class="fas fa-box w-5 mr-3 text-center"></i>
                            Products
                        </a>
                        
                        <!-- Users -->
                        <a href="users" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'users' ? 'active' : ''; ?>">
                            <i class="fas fa-users w-5 mr-3 text-center"></i>
                            Users
                        </a>
                        
                        <!-- Settings -->
                        <a href="settings" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'settings' ? 'active' : ''; ?>">
                            <i class="fas fa-cog w-5 mr-3 text-center"></i>
                            Settings
                        </a>
                        
                        <!-- Audit Trail -->
                        <a href="audit" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md <?php echo basename($_SERVER['REQUEST_URI']) == 'audit' ? 'active' : ''; ?>">
                            <i class="fas fa-history w-5 mr-3 text-center"></i>
                            Audit Trail
                        </a>
                    <?php endif; ?>
                </nav>
                
                <!-- Logout -->
                <div class="mt-auto pt-4 border-t border-primary-700">
                    <a href="profile" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md">
                        <i class="fas fa-user-cog w-5 mr-3 text-center"></i>
                        My Profile
                    </a>
                    <a href="logout" class="sidebar-item flex items-center px-3 py-2.5 text-sm rounded-md text-red-300 hover:text-red-200">
                        <i class="fas fa-sign-out-alt w-5 mr-3 text-center"></i>
                        Logout
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="content flex-grow h-screen overflow-y-auto transition-all duration-300 md:ml-64 pt-16">
            <!-- Top Nav for mobile -->
            <div class="md:hidden bg-primary-800 text-white p-4 flex items-center justify-between mt-16">
                <div class="flex items-center">
                    <span class="font-bold">Menu</span>
                </div>
                <div class="text-sm">
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="p-6">
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="mb-6 px-4 py-3 rounded-lg shadow-sm <?php echo $_SESSION['flash_type'] === 'error' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200'; ?>">
                        <div class="flex items-center">
                            <i class="<?php echo $_SESSION['flash_type'] === 'error' ? 'fas fa-exclamation-circle text-red-500' : 'fas fa-check-circle text-green-500'; ?> mr-3"></i>
                            <span>
                                <?php 
                                echo htmlspecialchars($_SESSION['flash_message']);
                                unset($_SESSION['flash_message']);
                                unset($_SESSION['flash_type']);
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php include $content; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Content for non-logged-in users -->
        <div class="w-full">
            <?php include $content; ?>
        </div>
    <?php endif; ?>

    <script>
        // Mobile navigation
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const openSidebar = document.getElementById('openSidebar');
            const closeSidebar = document.getElementById('closeSidebar');
            
            if (openSidebar && closeSidebar && sidebar) {
                openSidebar.addEventListener('click', function() {
                    sidebar.classList.add('open');
                });
                
                closeSidebar.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                });
            }
            
            // Dropdown functionality for mobile
            const dropdowns = document.querySelectorAll('.group');
            dropdowns.forEach(dropdown => {
                const button = dropdown.querySelector('button');
                const menu = dropdown.querySelector('.group-hover\\:block');
                
                if (button && menu) {
                    button.addEventListener('click', function() {
                        menu.classList.toggle('hidden');
                    });
                    
                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!dropdown.contains(event.target)) {
                            menu.classList.add('hidden');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
