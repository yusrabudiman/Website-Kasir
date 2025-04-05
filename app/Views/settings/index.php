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
        <h1 class="text-2xl font-semibold text-gray-900">Store Settings</h1>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mb-4 px-4 py-3 rounded-md <?php echo $_SESSION['flash_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>" id="flashMessage">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="<?php echo $_SESSION['flash_type'] === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?> mr-2"></i>
                    <span><?php echo $_SESSION['flash_message']; ?></span>
                </div>
                <button type="button" onclick="document.getElementById('flashMessage').style.display='none';" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php 
        // Clear flash message after displaying
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <form action="/settings/update" method="POST" class="p-6 space-y-6" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <!-- Store Information Section -->
            <div class="border-b pb-4">
                <h2 class="text-lg font-medium mb-4">Store Information</h2>
                
                <!-- Store Name -->
                <div class="mb-4">
                    <label for="store_name" class="block text-sm font-medium text-gray-700">Store Name *</label>
                    <input type="text" id="store_name" name="store_name" required 
                           value="<?php echo htmlspecialchars($settings->store_name ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Store Address -->
                <div class="mb-4">
                    <label for="store_address" class="block text-sm font-medium text-gray-700">Store Address</label>
                    <textarea id="store_address" name="store_address" rows="3"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($settings->address ?? ''); ?></textarea>
                </div>

                <!-- Store Phone -->
                <div class="mb-4">
                    <label for="store_phone" class="block text-sm font-medium text-gray-700">Store Phone</label>
                    <input type="text" id="store_phone" name="store_phone" 
                           value="<?php echo htmlspecialchars($settings->phone ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Store Email -->
                <div class="mb-4">
                    <label for="store_email" class="block text-sm font-medium text-gray-700">Store Email</label>
                    <input type="email" id="store_email" name="store_email" 
                           value="<?php echo htmlspecialchars($settings->email ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Store Logo -->
                <div>
                    <label for="store_logo" class="block text-sm font-medium text-gray-700">Store Logo</label>
                    <?php if (!empty($settings->store_logo)): ?>
                        <div class="mt-2 mb-2">
                            <img src="/public/uploads/<?php echo $settings->store_logo; ?>" 
                                alt="Store Logo" class="h-20 w-auto">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="store_logo" name="store_logo" accept="image/*"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    <p class="mt-1 text-sm text-gray-500">Upload a logo (max 2MB, JPG, PNG, GIF)</p>
                </div>
            </div>

            <!-- Business Settings Section -->
            <div class="border-b pb-4">
                <h2 class="text-lg font-medium mb-4">Business Settings</h2>
                
                <!-- Tax Percentage -->
                <div class="mb-4">
                    <label for="tax_percentage" class="block text-sm font-medium text-gray-700">Tax Percentage (%)</label>
                    <input type="number" id="tax_percentage" name="tax_percentage" step="0.01" min="0" max="100" 
                           value="<?php echo htmlspecialchars($settings->tax_percentage ?? '11.00'); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Currency Symbol -->
                <div class="mb-4">
                    <label for="currency_symbol" class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" 
                           value="<?php echo htmlspecialchars($settings->currency_symbol ?? 'Rp'); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Low Stock Threshold -->
                <div>
                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="1" 
                           value="<?php echo htmlspecialchars($settings->low_stock_threshold ?? '10'); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Products will be marked as "Low Stock" when quantity falls below this number</p>
                </div>
            </div>

            <!-- Receipt Settings Section -->
            <div>
                <h2 class="text-lg font-medium mb-4">Receipt Settings</h2>
                
                <!-- Receipt Footer -->
                <div>
                    <label for="receipt_footer" class="block text-sm font-medium text-gray-700">Receipt Footer Text</label>
                    <textarea id="receipt_footer" name="receipt_footer" rows="3"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($settings->receipt_footer ?? 'Thank you for your purchase! Please come again.'); ?></textarea>
                    <p class="mt-1 text-sm text-gray-500">This text will appear at the bottom of customer receipts</p>
                </div>
            </div>

            <div class="pt-4 flex justify-between">
                <a href="/dashboard" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <button type="submit"
                        class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-hide flash message after 5 seconds
setTimeout(function() {
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        flashMessage.style.opacity = '0';
        flashMessage.style.transition = 'opacity 1s';
        setTimeout(function() {
            flashMessage.style.display = 'none';
        }, 1000);
    }
}, 5000);
</script>
</body>
</html> 