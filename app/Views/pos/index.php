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

<!-- Back to Dashboard Button -->
<div class="p-4 flex justify-between items-center">
    <div class="flex items-center">
        <span class="text-indigo-600 text-xl font-bold"><?php echo $storeName ?? 'POS System'; ?></span>
    </div>
    <div>
        <input type="hidden" id="csrf_token" value="<?php echo $csrf_token; ?>">
        <a href="/dashboard" class="inline-flex items-center text-gray-700 hover:text-indigo-600">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>
</div>

<div class="flex h-[calc(100vh-4rem)] gap-6">
    
    <!-- Left Side - Product Selection -->
    <div class="w-2/3 bg-white shadow-sm rounded-lg p-6 overflow-hidden flex flex-col">
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
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

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto">
            <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Products will be loaded here dynamically -->
            </div>
        </div>
    </div>

    <!-- Right Side - Cart -->
    <div class="w-1/3 bg-white shadow-sm rounded-lg p-6 flex flex-col">
        <!-- Cart Header -->
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Current Order</h2>
            <p class="text-sm text-gray-500">Invoice: <span id="invoiceNumber">-</span></p>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto mb-4">
            <table class="min-w-full" id="cartTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="cartItems">
                    <!-- Cart items will be added here dynamically -->
                </tbody>
            </table>
        </div>

        <!-- Cart Summary -->
        <div class="border-t border-gray-200 pt-4 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium" id="subtotal"><?php echo $currency_symbol; ?> 0</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax (<?php echo $tax_rate; ?>%):</span>
                <span class="font-medium" id="tax"><?php echo $currency_symbol; ?> 0</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Service Charge (<?php echo $service_charge_amount; ?>%):</span>
                <span class="font-medium" id="serviceCharge"><?php echo $currency_symbol; ?> 0</span>
            </div>
            <div class="flex justify-between text-lg font-bold">
                <span>Total:</span>
                <span id="total"><?php echo $currency_symbol; ?> 0</span>
            </div>
            
            <!-- Payment Input -->
            <div class="space-y-2">
                <label for="payment" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm"><?php echo $currency_symbol; ?></span>
                    </div>
                    <input type="number" id="payment" name="payment" 
                           class="block w-full pl-12 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="0">
                </div>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Change:</span>
                <span class="font-medium" id="change"><?php echo $currency_symbol; ?> 0</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 space-y-2">
            <button id="clearCartBtn" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Clear Cart
            </button>
            <button id="processOrderBtn" class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Process Order (F8)
            </button>
        </div>
    </div>
</div>

<!-- Product Template (Hidden) -->
<template id="productTemplate">
    <div class="product-item bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-indigo-500 hover:shadow-sm transition-all">
        <div class="font-medium text-gray-900 product-name"></div>
        <div class="text-sm text-gray-500 product-code"></div>
        <div class="mt-2 flex justify-between items-baseline">
            <span class="text-indigo-600 font-medium product-price"></span>
            <span class="text-sm text-gray-500 product-stock"></span>
        </div>
    </div>
</template>

<!-- Cart Item Template (Hidden) -->
<template id="cartItemTemplate">
    <tr>
        <td class="px-3 py-2">
            <div class="text-sm text-gray-900 cart-item-name"></div>
            <div class="text-xs text-gray-500 cart-item-code"></div>
        </td>
        <td class="px-3 py-2">
            <input type="number" min="1" class="cart-item-qty w-16 px-2 py-1 text-right border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </td>
        <td class="px-3 py-2 text-right text-sm cart-item-price"></td>
        <td class="px-3 py-2 text-right text-sm font-medium cart-item-total"></td>
        <td class="px-3 py-2 text-right">
            <button class="text-red-600 hover:text-red-900 cart-item-remove">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Loading Spinner -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
</div>

<script>
// Constants
const TAX_RATE = <?php echo $tax_rate; ?>;
const SERVICE_CHARGE_AMOUNT = <?php echo isset($service_charge_amount) ? $service_charge_amount : 0; ?>;
const CSRF_TOKEN = document.getElementById('csrf_token').value;

// Cart state
let cart = [];
let subtotal = 0;
let tax = 0;
let serviceCharge = 0;
let total = 0;

// DOM Elements
const searchInput = document.getElementById('searchProduct');
const productGrid = document.getElementById('productGrid');
const cartItems = document.getElementById('cartItems');
const subtotalEl = document.getElementById('subtotal');
const taxEl = document.getElementById('tax');
const serviceChargeEl = document.getElementById('serviceCharge');
const totalEl = document.getElementById('total');
const paymentInput = document.getElementById('payment');
const changeEl = document.getElementById('change');
const clearCartBtn = document.getElementById('clearCartBtn');
const processOrderBtn = document.getElementById('processOrderBtn');
const loadingOverlay = document.getElementById('loadingOverlay');
const searchLoading = document.getElementById('searchLoading');

// Templates
const productTemplate = document.getElementById('productTemplate');
const cartItemTemplate = document.getElementById('cartItemTemplate');

// Event Listeners
searchInput.addEventListener('input', debounce(searchProducts, 300));
paymentInput.addEventListener('input', calculateChange);
clearCartBtn.addEventListener('click', clearCart);
processOrderBtn.addEventListener('click', processOrder);

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (e.key === 'F8') {
        e.preventDefault();
        processOrder();
    }
});

// Functions
async function searchProducts() {
    const search = searchInput.value.trim();
    
    // Show loading indicator
    searchLoading.classList.remove('hidden');
    productGrid.innerHTML = `
        <div class="col-span-full text-center py-4 text-gray-500">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto mb-2"></div>
            <div>Searching products...</div>
        </div>
    `;

    try {
        const response = await fetch('/pos/search-product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `search=${encodeURIComponent(search)}&csrf_token=${CSRF_TOKEN}`
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Failed to search products');
        }
        
        displayProducts(data.products);
    } catch (error) {
        console.error('Error searching products:', error);
        productGrid.innerHTML = `
            <div class="col-span-full text-center py-4 text-red-500">
                <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                <div>${error.message}</div>
                <button onclick="searchProducts()" class="mt-2 text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-redo mr-1"></i> Try Again
                </button>
            </div>
        `;
    } finally {
        // Hide loading indicator
        searchLoading.classList.add('hidden');
    }
}

function displayProducts(products) {
    productGrid.innerHTML = '';
    if (!products || products.length === 0) {
        productGrid.innerHTML = `
            <div class="col-span-full text-center py-4 text-gray-500">
                <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                <div>No products found</div>
                <div class="text-sm mt-2">Try a different search term</div>
            </div>
        `;
        return;
    }

    products.forEach(product => {
        const productEl = productTemplate.content.cloneNode(true);
        const container = productEl.querySelector('.product-item');
        
        container.querySelector('.product-name').textContent = product.name;
        container.querySelector('.product-code').textContent = product.code;
        container.querySelector('.product-price').textContent = formatCurrency(product.price);
        container.querySelector('.product-stock').textContent = `Stock: ${product.stock}`;
        
        // Add click event to add product to cart
        container.addEventListener('click', () => {
            addToCart(product);
            // Show success feedback
            const feedback = document.createElement('div');
            feedback.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg';
            feedback.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${product.name} added to cart</span>
                </div>
            `;
            document.body.appendChild(feedback);
            setTimeout(() => feedback.remove(), 2000);
        });
        
        // Add hover effect
        container.addEventListener('mouseenter', () => {
            container.classList.add('border-indigo-500', 'shadow-sm');
        });
        container.addEventListener('mouseleave', () => {
            container.classList.remove('border-indigo-500', 'shadow-sm');
        });
        
        productGrid.appendChild(productEl);
    });
}

function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
            updateCartItem(existingItem);
        }
    } else {
        const cartItem = {
            id: product.id,
            code: product.code,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            stock: parseInt(product.stock)
        };
        cart.push(cartItem);
        addCartItemToDOM(cartItem);
    }
    
    updateTotals();
}

function addCartItemToDOM(item) {
    const cartItemEl = cartItemTemplate.content.cloneNode(true);
    const row = cartItemEl.querySelector('tr');
    
    row.dataset.productId = item.id;
    row.querySelector('.cart-item-name').textContent = item.name;
    row.querySelector('.cart-item-code').textContent = item.code;
    
    const qtyInput = row.querySelector('.cart-item-qty');
    qtyInput.value = item.quantity;
    qtyInput.max = item.stock;
    qtyInput.addEventListener('change', () => updateCartItemQuantity(item.id, qtyInput.value));
    
    row.querySelector('.cart-item-price').textContent = formatCurrency(item.price);
    row.querySelector('.cart-item-total').textContent = formatCurrency(item.price * item.quantity);
    
    row.querySelector('.cart-item-remove').addEventListener('click', () => removeFromCart(item.id));
    
    cartItems.appendChild(row);
}

function updateCartItem(item) {
    const row = cartItems.querySelector(`tr[data-product-id="${item.id}"]`);
    if (row) {
        row.querySelector('.cart-item-qty').value = item.quantity;
        row.querySelector('.cart-item-total').textContent = formatCurrency(item.price * item.quantity);
    }
}

function updateCartItemQuantity(productId, quantity) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        quantity = parseInt(quantity);
        if (quantity > item.stock) {
            quantity = item.stock;
        } else if (quantity < 1) {
            quantity = 1;
        }
        item.quantity = quantity;
        updateCartItem(item);
        updateTotals();
    }
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    const row = cartItems.querySelector(`tr[data-product-id="${productId}"]`);
    if (row) {
        row.remove();
    }
    updateTotals();
}

function updateTotals() {
    subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
    tax = subtotal * (TAX_RATE / 100);
    serviceCharge = subtotal * (SERVICE_CHARGE_AMOUNT / 100);
    total = subtotal + tax + serviceCharge;
    
    subtotalEl.textContent = formatCurrency(subtotal);
    taxEl.textContent = formatCurrency(tax);
    serviceChargeEl.textContent = formatCurrency(serviceCharge);
    totalEl.textContent = formatCurrency(total);
    
    calculateChange();
}

function calculateChange() {
    const payment = parseFloat(paymentInput.value) || 0;
    const change = payment - total;
    changeEl.textContent = formatCurrency(Math.max(0, change));
    
    // Enable/disable process button based on payment
    processOrderBtn.disabled = payment < total;
    processOrderBtn.classList.toggle('opacity-50', payment < total);
}

function formatCurrency(amount) {
    if (isNaN(amount)) return `<?php echo $currency_symbol; ?> 0`;
    return `<?php echo $currency_symbol; ?> ${parseFloat(amount).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}

function clearCart() {
    cart = [];
    cartItems.innerHTML = '';
    updateTotals();
    paymentInput.value = '';
}

async function processOrder() {
    if (cart.length === 0) {
        alert('Please add items to cart');
        return;
    }

    const payment = parseFloat(paymentInput.value) || 0;
    if (payment < total) {
        alert('Insufficient payment amount');
        return;
    }

    loadingOverlay.classList.remove('hidden');

    try {
        // Format items data
        const formattedItems = cart.map(item => {
            // Debug each item
            console.log('Processing cart item:', item);
            
            // Validate required fields
            if (!item.id || !item.code || !item.name || !item.quantity || !item.price) {
                console.error('Invalid item data:', item);
                throw new Error('Invalid item data in cart');
            }

            return {
                product_id: item.id,
                code: item.code,
                name: item.name,
                quantity: parseInt(item.quantity),
                price: parseFloat(item.price),
                subtotal: parseFloat(item.price * item.quantity)
            };
        });

        // Debug log
        console.log('Cart items:', cart);
        console.log('Formatted items:', formattedItems);
        console.log('Payment amount:', payment);
        console.log('Total amount:', total);
        console.log('Tax amount:', tax);
        console.log('Service charge:', serviceCharge);
        console.log('Change amount:', payment - total);

        const formData = new FormData();
        formData.append('csrf_token', CSRF_TOKEN);
        formData.append('items', JSON.stringify(formattedItems));
        formData.append('total_amount', subtotal.toFixed(2));
        formData.append('tax_amount', tax.toFixed(2));
        formData.append('service_charge_amount', serviceCharge.toFixed(2));
        formData.append('final_amount', total.toFixed(2));
        formData.append('payment_amount', payment.toFixed(2));
        formData.append('change_amount', (payment - total).toFixed(2));

        // Debug form data
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        const response = await fetch('/pos/create-order', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        console.log('Server response:', result);
        
        if (result.success) {
            // Ubah URL dari print-receipt menjadi receipt
            window.location.href = `/pos/receipt/${result.order_id}`;

            
            // Clear cart
            clearCart();
            
            // Reset search
            searchInput.value = '';
            productGrid.innerHTML = '';
            
            // Show success message
            // alert('Order processed successfully!');
        } else {
            throw new Error(result.error || 'Failed to process order');
        }
    } catch (error) {
        console.error('Error processing order:', error);
        alert('Error processing order: ' + error.message);
    } finally {
        loadingOverlay.classList.add('hidden');
    }
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

// Initial load
searchProducts();
</script>
