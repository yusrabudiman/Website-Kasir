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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Store Name -->
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700">Store Name *</label>
                        <input type="text" id="store_name" name="store_name" required 
                               value="<?php echo htmlspecialchars($settings->store_name ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Store Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Store Address</label>
                        <textarea id="address" name="address" rows="3"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($settings->address ?? ''); ?></textarea>
                    </div>

                    <!-- Store Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Store Phone</label>
                        <input type="text" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($settings->phone ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Store Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Store Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($settings->email ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Store Logo -->
                    <div class="md:col-span-2">
                        <label for="logo" class="block text-sm font-medium text-gray-700">Store Logo</label>
                        <?php 
                        $logoPath = __DIR__ . '/../../public/uploads/' . ($settings->logo ?? '');
                        if (!empty($settings->logo) && file_exists($logoPath)): 
                            // Extract filename from full path
                            $logoFilename = basename($settings->logo);
                        ?>
                            <div class="mt-2 mb-2">
                                <img src="/uploads/<?php echo $logoFilename; ?>" 
                                    alt="Store Logo" 
                                    class="h-64 w-64 object-contain cursor-pointer hover:opacity-75 transition-opacity"
                                    onclick="showImagePreview(this.src)"
                                    id="logoPreview">
                            </div>
                        <?php else: ?>
                            <div class="mt-2 mb-2">
                                <div class="h-64 w-64 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-sm text-gray-500">No logo uploaded</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="mt-2">
                            <label class="block">
                                <span class="sr-only">Choose file</span>
                                <input type="file" id="logo" name="logo" accept="image/*"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100"
                                       onchange="previewLogo(this)">
                            </label>
                            <p class="mt-1 text-sm text-gray-500">Upload a logo (max 2MB, JPG, PNG, GIF)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Preview Modal -->
            <div id="imagePreviewModal" class="fixed hidden inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 w-full max-w-2xl">
                    <div class="bg-white rounded-lg shadow-xl">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Image Preview</h3>
                            <button type="button" onclick="closeImagePreview()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <img id="previewImage" src="" alt="Preview" class="max-w-full h-auto mx-auto">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Settings Section -->
            <div class="border-b pb-4">
                <h2 class="text-lg font-medium mb-4">Business Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tax Rate -->
                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                        <input type="number" id="tax_rate" name="tax_rate" step="0.01" min="0" max="100" 
                               value="<?php echo htmlspecialchars($settings->tax_rate ?? '0'); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Service Charge -->
                    <div>
                        <label for="service_charge" class="block text-sm font-medium text-gray-700">Service Charge (%)</label>
                        <input type="number" id="service_charge" name="service_charge" step="0.01" min="0" max="100" 
                               value="<?php echo htmlspecialchars($settings->service_charge ?? '0'); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Low Stock Threshold -->
                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                        <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="1" 
                               value="<?php echo htmlspecialchars($settings->low_stock_threshold ?? '10'); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Minimum stock level before warning is triggered</p>
                    </div>

                    <!-- Currency Symbol -->
                    <div>
                        <label for="currency_symbol" class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                        <input type="text" id="currency_symbol" name="currency_symbol" 
                               value="<?php echo htmlspecialchars($settings->currency_symbol ?? 'Rp'); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Currency symbol used in the system (e.g., Rp, $, €)</p>
                    </div>

                    <!-- Printer Name -->
                    <div>
                        <label for="printer_name" class="block text-sm font-medium text-gray-700">Printer Name</label>
                        <input type="text" id="printer_name" name="printer_name" 
                               value="<?php echo htmlspecialchars($settings->printer_name ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Printer Type -->
                    <div>
                        <label for="printer_type" class="block text-sm font-medium text-gray-700">Printer Type</label>
                        <select id="printer_type" name="printer_type" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="thermal" <?php echo ($settings->printer_type ?? '') === 'thermal' ? 'selected' : ''; ?>>Thermal Printer</option>
                            <option value="regular" <?php echo ($settings->printer_type ?? '') === 'regular' ? 'selected' : ''; ?>>Regular Printer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Receipt Settings Section -->
            <div>
                <h2 class="text-lg font-medium mb-4">Receipt Settings</h2>
                <!-- Thank You Message -->
                <div>
                    <label for="thank_you_message" class="block text-sm font-medium text-gray-700">Thank You Message</label>
                    <textarea id="thank_you_message" name="thank_you_message" rows="3"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($settings->thank_you_message ?? 'Terima kasih telah berbelanja di toko kami. Kami menghargai kepercayaan Anda dan berharap dapat melayani Anda kembali.'); ?></textarea>
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

    <!-- History Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Settings Changes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    $count = 0;
                    foreach ($history as $record): 
                        if ($count >= 3) break;
                        $count++;
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('Y-m-d H:i:s', strtotime($record->created_at)); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($record->user_name ?? $record->username ?? 'System'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Settings
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            Store settings updated
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($record->ip_address ?? '::1'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button onclick="showDetails(<?php echo htmlspecialchars(json_encode($record->details)); ?>)" 
                                    class="text-indigo-600 hover:text-indigo-900">
                                View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Details -->
    <div id="detailsModal" class="fixed hidden inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Change Details</h3>
                <div id="detailsContent" class="mt-2 px-7 py-3 text-sm text-gray-500 space-y-2">
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showDetails(details) {
        const modal = document.getElementById('detailsModal');
        const content = document.getElementById('detailsContent');
        
        if (details) {
            const changes = details.split('; ');
            let html = '';
            changes.forEach(change => {
                // Extract old and new values
                const match = change.match(/Updated (.*?) from (.*?) to (.*)/);
                if (match) {
                    const [_, field, oldValue, newValue] = match;
                    html += `
                        <div class="mb-4">
                            <div class="flex items-start">
                                <span class="text-indigo-600 mr-2">•</span>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">${field}</div>
                                    <div class="mt-1 grid grid-cols-2 gap-2">
                                        <div class="bg-gray-50 p-2 rounded">
                                            <div class="text-xs text-gray-500">Previous Value</div>
                                            <div class="text-gray-700">${oldValue}</div>
                                        </div>
                                        <div class="bg-green-50 p-2 rounded">
                                            <div class="text-xs text-green-500">New Value</div>
                                            <div class="text-green-700">${newValue}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        ${getChangeImpact(field)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (change.includes('Added')) {
                    const [_, field, value] = change.match(/Added (.*?): (.*)/);
                    html += `
                        <div class="mb-4">
                            <div class="flex items-start">
                                <span class="text-green-600 mr-2">+</span>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">${field}</div>
                                    <div class="mt-1 bg-green-50 p-2 rounded">
                                        <div class="text-green-700">${value}</div>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        ${getChangeImpact(field)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (change.includes('Removed')) {
                    const [_, field, value] = change.match(/Removed (.*?): (.*)/);
                    html += `
                        <div class="mb-4">
                            <div class="flex items-start">
                                <span class="text-red-600 mr-2">-</span>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">${field}</div>
                                    <div class="mt-1 bg-red-50 p-2 rounded">
                                        <div class="text-red-700">${value}</div>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        ${getChangeImpact(field)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            content.innerHTML = html;
        } else {
            content.innerHTML = '<p>No details available</p>';
        }
        
        modal.classList.remove('hidden');
    }

    function getChangeImpact(field) {
        const impacts = {
            'Store Name': 'Perubahan ini akan mempengaruhi tampilan di header aplikasi dan struk pembayaran',
            'Store Address': 'Alamat toko akan ditampilkan di struk pembayaran dan informasi kontak',
            'Phone Number': 'Nomor telepon akan ditampilkan di struk pembayaran dan informasi kontak',
            'Email Address': 'Email akan digunakan untuk notifikasi dan informasi kontak',
            'Tax Rate': 'Perubahan persentase pajak akan mempengaruhi perhitungan pajak di setiap transaksi',
            'Service Charge': 'Perubahan persentase service charge akan mempengaruhi perhitungan biaya layanan di setiap transaksi',
            'Printer Name': 'Nama printer akan digunakan untuk mencetak struk pembayaran',
            'Printer Type': 'Tipe printer menentukan format cetak struk pembayaran',
            'Thank You Message': 'Pesan terima kasih akan ditampilkan di struk pembayaran',
            'store logo': 'Logo toko akan ditampilkan di header aplikasi dan struk pembayaran'
        };
        return impacts[field] || 'Perubahan ini akan mempengaruhi pengaturan toko';
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('detailsModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (!preview) {
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'mt-2 mb-2';
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Store Logo" class="h-64 w-64 object-contain cursor-pointer hover:opacity-75 transition-opacity" onclick="showImagePreview(this.src)" id="logoPreview">`;
                    input.parentNode.insertBefore(previewContainer, input);
                } else {
                    preview.src = e.target.result;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showImagePreview(src) {
        const modal = document.getElementById('imagePreviewModal');
        const previewImage = document.getElementById('previewImage');
        previewImage.src = src;
        modal.classList.remove('hidden');
    }

    function closeImagePreview(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        document.getElementById('imagePreviewModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('imagePreviewModal');
        if (event.target == modal) {
            closeImagePreview(event);
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImagePreview(event);
        }
    });
    </script>
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