<!-- Navigation Bar -->
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
                <a href="/dashboard" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['REQUEST_URI']) == 'dashboard' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50'; ?>">Dashboard</a>
                
                <a href="/pos" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['REQUEST_URI']) == 'pos' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50'; ?>">
                    Point of Sale
                </a>

                <a href="/products" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['REQUEST_URI']) == 'products' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50'; ?>">
                    Products
                </a>
                
                <a href="/users" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['REQUEST_URI']) == 'users' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50'; ?>">
                    Users
                </a>
                
                <a href="/reports" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['REQUEST_URI']) == 'reports' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50'; ?>">
                    Reports
                </a>
                
                <a href="/audit" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['REQUEST_URI']) == 'audit' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50'; ?>">
                    Audit Trail
                </a>
                
                <a href="/settings" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['REQUEST_URI']) == 'settings' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50'; ?>">
                    Settings
                </a>
            </div>
            
            <!-- User Profile Section -->
            <div class="flex items-center">
                <div class="relative inline-block text-left">
                    <button type="button" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true" onclick="toggleUserMenu()">
                        <span class="mr-2"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                            <?php echo substr($_SESSION['user_name'] ?? 'U', 0, 1); ?>
                        </div>
                    </button>
                    
                    <div class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" id="user-menu" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            <a href="/profile" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
                                Your Profile
                            </a>
                            <a href="/logout" class="text-gray-700 hover:bg-red-50 hover:text-red-600 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">
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
            <a href="/dashboard" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'dashboard' ? 'bg-indigo-50 text-indigo-600 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-indigo-600'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Dashboard</a>
            <a href="/pos" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'pos' ? 'bg-indigo-50 text-indigo-600 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-indigo-600'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Point of Sale</a>
            <a href="/products" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'products' ? 'bg-indigo-50 text-indigo-600 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-indigo-600'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Products</a>
            <a href="/users" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'users' ? 'bg-indigo-50 text-indigo-600 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-indigo-600'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Users</a>
            <a href="/reports" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'reports' ? 'bg-indigo-50 text-indigo-600 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-indigo-600'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Reports</a>
            <a href="/audit" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'audit' ? 'bg-indigo-50 text-indigo-600 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-indigo-600'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Audit Trail</a>
            <a href="/settings" class="<?php echo basename($_SERVER['REQUEST_URI']) == 'settings' ? 'bg-indigo-50 text-indigo-600 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-indigo-600'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Settings</a>
            
            <div class="border-t border-gray-200 pt-2">
                <a href="/profile" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Your Profile</a>
                <a href="/logout" class="text-gray-600 hover:bg-gray-50 hover:text-red-600 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Sign out</a>
            </div>
        </div>
    </div>
</nav>

<script>
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
    const userMenu = document.getElementById('user-menu');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (!e.target.closest('#user-menu-button') && userMenu && !userMenu.classList.contains('hidden')) {
        userMenu.classList.add('hidden');
    }
    
    if (!e.target.closest('button[aria-controls="mobile-menu"]') && mobileMenu && !mobileMenu.classList.contains('hidden') && window.innerWidth < 768) {
        mobileMenu.classList.add('hidden');
    }
});
</script> 