<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_ENV['APP_NAME'] ?? 'POS System'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Hero Section -->
    <header class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
        <div class="container mx-auto px-6 py-16 max-w-6xl">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                        Modern Point of Sale System
                    </h1>
                    <p class="text-xl text-indigo-100 mb-8">
                        Streamline your business operations with our powerful and user-friendly POS solution.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="/login" class="bg-white text-indigo-600 hover:bg-indigo-50 font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 text-center">
                            Sign In
                        </a>
                        <a href="/signup" class="bg-indigo-700 hover:bg-indigo-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 text-center">
                            Create Account
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <img src="https://cdn-icons-png.flaticon.com/512/2949/2949886.png" alt="POS System" class="w-full max-w-md mx-auto">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-6xl">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Key Features</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-6 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Easy Checkout</h3>
                    <p class="text-gray-600">Intuitive interface for fast and error-free transactions, reducing customer wait times.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="p-6 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Inventory Management</h3>
                    <p class="text-gray-600">Track stock levels in real-time and get alerts when items are running low.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="p-6 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Detailed Reports</h3>
                    <p class="text-gray-600">Access comprehensive sales analytics and reports to make data-driven decisions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-auto">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $_ENV['APP_NAME'] ?? 'POS System'; ?>. All rights reserved.</p>
            <p class="mt-2 text-gray-400 text-sm">A modern point of sale solution for your business</p>
        </div>
    </footer>
</body>
</html>
