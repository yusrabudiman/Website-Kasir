<div class="bg-white shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Edit Product</h2>
            <a href="/products" class="text-indigo-600 hover:text-indigo-900">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>

        <form method="POST" action="/products/edit/<?php echo htmlspecialchars($product->id); ?>" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Code (Read-only) -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Product Code</label>
                    <input type="text" id="code" value="<?php echo htmlspecialchars($product->code); ?>" readonly
                           class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    <p class="mt-1 text-sm text-gray-500">Product code cannot be changed</p>
                </div>

                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo htmlspecialchars($product->name); ?>"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price (Rp) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="price" name="price" required min="0" step="100"
                               value="<?php echo htmlspecialchars($product->price); ?>"
                               class="block w-full pl-12 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Current Stock</label>
                    <input type="number" id="stock" name="stock" min="0"
                           value="<?php echo htmlspecialchars($product->stock); ?>"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($product->description); ?></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="window.location.href='/products'"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </button>
                <button type="submit"
                        class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('price').addEventListener('input', function(e) {
    // Remove any non-numeric characters
    let value = this.value.replace(/[^0-9]/g, '');
    
    // Ensure the value is not empty
    if (value === '') {
        value = '0';
    }
    
    // Update the input value
    this.value = value;
});
</script>
