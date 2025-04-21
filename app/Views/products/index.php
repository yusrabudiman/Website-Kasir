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
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Products</h2>
                <a href="/products/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Add Product
                </a>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input type="hidden" id="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="text" id="searchProduct" 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Search products by name or code...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <div id="searchLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($product->code); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($product->name); ?></div>
                                <?php if ($product->description): ?>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($product->description); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                Rp <?php echo number_format($product->price, 0, ',', '.'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="<?php echo $product->stock <= 10 ? 'text-red-600 font-semibold' : 'text-gray-900'; ?>">
                                    <?php echo $product->stock; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="/products/edit/<?php echo $product->id; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="confirmDelete('<?php echo $product->id; ?>', '<?php echo htmlspecialchars($product->name); ?>')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Product</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this product? This action cannot be undone.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="deleteForm" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="button" onclick="closeDeleteModal()"
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const searchProduct = document.getElementById('searchProduct');
const productsTableBody = document.getElementById('productsTableBody');
const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const searchLoading = document.getElementById('searchLoading');

// Function to handle search
async function handleSearch(search) {
    // Show loading indicator
    searchLoading.classList.remove('hidden');
    productsTableBody.classList.add('opacity-50');
    
    try {
        const csrf_token = document.getElementById('csrf_token').value;
        const response = await fetch('/products/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: `search=${encodeURIComponent(search)}&csrf_token=${encodeURIComponent(csrf_token)}`
        });
        
        // Debug: Log raw response
        const responseText = await response.text();
        console.log('Raw server response:', responseText);
        
        try {
            const data = JSON.parse(responseText);
            if (data.success) {
                updateProductsTable(data.products);
            } else {
                throw new Error(data.error || 'Error searching products');
            }
        } catch (jsonError) {
            console.error('JSON Parse Error:', jsonError);
            throw new Error('Server returned invalid JSON response. Check server logs for details.');
        }
    } catch (error) {
        console.error('Error searching products:', error);
        productsTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-red-500">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    ${error.message}
                </td>
            </tr>
        `;
    } finally {
        // Hide loading indicator
        searchLoading.classList.add('hidden');
        productsTableBody.classList.remove('opacity-50');
    }
}

// Add event listener for search input
searchProduct.addEventListener('input', debounce((e) => {
    const search = e.target.value.trim();
    handleSearch(search);
}, 300));

// Initial search to load all products
handleSearch('');

function updateProductsTable(products) {
    if (!products || products.length === 0) {
        productsTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                    <div class="text-lg">No products found</div>
                    <div class="text-sm mt-2">Try a different search term</div>
                </td>
            </tr>
        `;
        return;
    }

    productsTableBody.innerHTML = products.map(product => `
        <tr class="transition-all duration-200 hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${escapeHtml(product.code)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${escapeHtml(product.name)}</div>
                ${product.description ? `<div class="text-sm text-gray-500">${escapeHtml(product.description)}</div>` : ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                Rp ${numberFormat(product.price)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                <span class="${product.stock <= 10 ? 'text-red-600 font-semibold' : 'text-gray-900'}">
                    ${product.stock}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="/products/edit/${product.id}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button onclick="confirmDelete('${product.id}', '${escapeHtml(product.name)}')" 
                        class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
}

function confirmDelete(productId, productName) {
    deleteForm.action = `/products/delete/${productId}`;
    deleteModal.classList.remove('hidden');
}

function closeDeleteModal() {
    deleteModal.classList.add('hidden');
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}
</script>
</body>
</html>
