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
        <h1 class="text-2xl font-semibold text-gray-900">Adjust Stock</h1>
        <a href="/stock" class="text-indigo-600 hover:text-indigo-900">
            <i class="fas fa-arrow-left mr-1"></i> Back to Stock Management
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-4 px-4 py-3 rounded-md <?php echo $_SESSION['flash']['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
            <?php echo $_SESSION['flash']['message']; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <form action="/stock/adjust" method="POST" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Product Selection -->
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">Product</label>
                <select id="product_id" name="product_id" required
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                    <option value="">Select a product</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product->id; ?>" data-stock="<?php echo $product->stock; ?>">
                            <?php echo htmlspecialchars($product->code . ' - ' . $product->name); ?> 
                            (Current Stock: <?php echo $product->stock; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Adjustment Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Adjustment Type</label>
                <select id="type" name="type" required
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                    <option value="purchase">Purchase (Add Stock)</option>
                    <option value="adjustment">Manual Adjustment (Add/Remove)</option>
                    <option value="return">Return (Remove Stock)</option>
                </select>
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" name="quantity" id="quantity" required min="1"
                           class="block w-full pr-10 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md"
                           placeholder="Enter quantity">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">units</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <div class="mt-1">
                    <textarea id="notes" name="notes" rows="3"
                              class="shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
                              placeholder="Enter reason for adjustment"></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Stock Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const quantityInput = document.getElementById('quantity');
    const productSelect = document.getElementById('product_id');

    function updateQuantityValidation() {
        const selectedProduct = productSelect.options[productSelect.selectedIndex];
        const currentStock = selectedProduct ? parseInt(selectedProduct.dataset.stock) || 0 : 0;
        const type = typeSelect.value;

        // Reset validation
        quantityInput.min = 1;
        quantityInput.max = '';

        // Add validation based on type
        if (type === 'return') {
            quantityInput.max = currentStock;
        }
    }

    typeSelect.addEventListener('change', updateQuantityValidation);
    productSelect.addEventListener('change', updateQuantityValidation);
    updateQuantityValidation();
});
</script>
