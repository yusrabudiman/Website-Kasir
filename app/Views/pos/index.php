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
                <span class="font-medium" id="subtotal">Rp 0</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax (<?php echo $tax_rate; ?>%):</span>
                <span class="font-medium" id="tax">Rp 0</span>
            </div>
            <div class="flex justify-between text-lg font-bold">
                <span>Total:</span>
                <span id="total">Rp 0</span>
            </div>
            
            <!-- Payment Input -->
            <div class="space-y-2">
                <label for="payment" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" id="payment" name="payment" 
                           class="block w-full pl-12 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="0">
                </div>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Change:</span>
                <span class="font-medium" id="change">Rp 0</span>
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
const CSRF_TOKEN = '<?php echo $csrf_token; ?>';

// Cart state
let cart = [];
let subtotal = 0;
let tax = 0;
let total = 0;

// DOM Elements
const searchInput = document.getElementById('searchProduct');
const productGrid = document.getElementById('productGrid');
const cartItems = document.getElementById('cartItems');
const subtotalEl = document.getElementById('subtotal');
const taxEl = document.getElementById('tax');
const totalEl = document.getElementById('total');
const paymentInput = document.getElementById('payment');
const changeEl = document.getElementById('change');
const clearCartBtn = document.getElementById('clearCartBtn');
const processOrderBtn = document.getElementById('processOrderBtn');
const loadingOverlay = document.getElementById('loadingOverlay');

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
    const search = searchInput.value;
    try {
        const response = await fetch('/pos/search-product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `search=${encodeURIComponent(search)}&csrf_token=${CSRF_TOKEN}`
        });
        
        const data = await response.json();
        displayProducts(data.products);
    } catch (error) {
        console.error('Error searching products:', error);
    }
}

function displayProducts(products) {
    productGrid.innerHTML = '';
    products.forEach(product => {
        const productEl = productTemplate.content.cloneNode(true);
        const container = productEl.querySelector('.product-item');
        
        container.querySelector('.product-name').textContent = product.name;
        container.querySelector('.product-code').textContent = product.code;
        container.querySelector('.product-price').textContent = formatCurrency(product.price);
        container.querySelector('.product-stock').textContent = `Stock: ${product.stock}`;
        
        container.addEventListener('click', () => addToCart(product));
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
            price: product.price,
            quantity: 1,
            stock: product.stock
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
    subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    tax = subtotal * (TAX_RATE / 100);
    total = subtotal + tax;
    
    subtotalEl.textContent = formatCurrency(subtotal);
    taxEl.textContent = formatCurrency(tax);
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
        const response = await fetch('/pos/create-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    price: item.price,
                    subtotal: item.price * item.quantity
                })),
                total_amount: subtotal,
                tax_amount: tax,
                final_amount: total,
                payment_amount: payment,
                change_amount: payment - total,
                csrf_token: CSRF_TOKEN
            })
        });

        const result = await response.json();
        
        if (result.success) {
            // Print receipt
            window.open(`/pos/print-receipt/${result.order_id}`, '_blank');
            
            // Clear cart
            clearCart();
            
            // Reset search
            searchInput.value = '';
            productGrid.innerHTML = '';
        } else {
            throw new Error(result.error || 'Failed to process order');
        }
    } catch (error) {
        alert('Error processing order: ' + error.message);
    } finally {
        loadingOverlay.classList.add('hidden');
    }
}

function formatCurrency(amount) {
    return `Rp ${amount.toLocaleString('id-ID')}`;
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
