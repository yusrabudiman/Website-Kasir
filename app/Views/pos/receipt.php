<!DOCTYPE html>
<html>
<head>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tambahkan QR Code library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <meta charset="utf-8">
    <title>Receipt #<?php echo $order->invoice_number; ?></title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 10px;
            width: 80mm;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .store-name { font-size: 16px; }
        table { width: 100%; }
        th, td { padding: 2px 0; }
        .item-name { max-width: 160px; }
        @media print {
            .no-print { display: none; }
        }
        .qr-code {
            text-align: center;
            margin: 10px 0;
        }
        .social-info {
            font-size: 10px;
            text-align: center;
            margin-top: 5px;
        }
        .stock-info {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Store Info -->
    <div class="text-center mb-2">
        <div class="store-name font-bold mb-1"><?php echo htmlspecialchars($settings->store_name); ?></div>
        <div class="mb-1"><?php echo nl2br(htmlspecialchars($settings->address)); ?></div>
        <div class="mb-2"><?php echo htmlspecialchars($settings->phone); ?></div>
        <?php if (isset($settings->logo) && !empty($settings->logo)): ?>
            <img src="<?php echo $settings->logo; ?>" alt="Store Logo" class="mx-auto mb-2" style="max-width: 100px;">
        <?php endif; ?>
    </div>

    <!-- QR Code -->
    <div class="qr-code" id="qrcode"></div>

    <!-- Order Info -->
    <div class="border-bottom border-top py-1">
        <table>
            <tr>
                <td>Invoice</td>
                <td>: <?php echo htmlspecialchars($order->invoice_number); ?></td>
            </tr>
            <tr>
                <td>Date</td>
                <td>: <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
            </tr>
            <tr>
                <td>Cashier</td>
                <td>: <?php echo htmlspecialchars($order->cashier_name); ?></td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td>: Cash</td>
            </tr>
        </table>
    </div>

    <!-- Order Items -->
    <table class="border-bottom py-1">
        <tr>
            <th class="text-left">Item</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Price</th>
            <th class="text-right">Total</th>
        </tr>
        <?php foreach ($order->items as $item): ?>
        <tr>
            <td class="item-name">
                <?php echo htmlspecialchars($item->name); ?>
                <?php if (isset($item->stock)): ?>
                    <div class="stock-info">Stock: <?php echo $item->stock; ?></div>
                <?php endif; ?>
            </td>
            <td class="text-right"><?php echo $item->quantity; ?></td>
            <td class="text-right"><?php echo number_format($item->price, 0, ',', '.'); ?></td>
            <td class="text-right"><?php echo number_format($item->subtotal, 0, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Order Summary -->
    <table class="py-1">
        <tr>
            <td>Subtotal</td>
            <td class="text-right"><?php echo number_format($order->total_amount, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Tax (<?php echo $settings->tax_percentage; ?>%)</td>
            <td class="text-right"><?php echo number_format($order->tax_amount, 0, ',', '.'); ?></td>
        </tr>
        <tr class="font-bold border-top py-1">
            <td>Total</td>
            <td class="text-right"><?php echo number_format($order->final_amount, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Payment</td>
            <td class="text-right"><?php echo number_format($order->payment_amount, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Change</td>
            <td class="text-right"><?php echo number_format($order->change_amount, 0, ',', '.'); ?></td>
        </tr>
    </table>

    <!-- Enhanced Footer -->
    <div class="text-center py-1 border-top">
        <p class="mb-1"><?php echo htmlspecialchars($settings->thank_you_message); ?></p>
        <div class="social-info">
            <p>Business Hours: 09:00 - 22:00</p>
            <p>Follow us on:</p>
            <p>Instagram: @yourstorename</p>
            <p>Website: www.yourstore.com</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded">
            <i class="fas fa-print mr-2"></i> Print Receipt
        </button>
        <button onclick="window.location.href='/pos'" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">
            <i class="fas fa-arrow-left mr-2"></i> Back to POS
        </button>
    </div>

    <script>
        // Generate QR Code
        window.onload = function() {
            var qr = qrcode(0, 'M');
            qr.addData('<?php echo $order->invoice_number; ?>');
            qr.make();
            document.getElementById('qrcode').innerHTML = qr.createImgTag(4);
            
            // Auto print
            window.print();
        }
    </script>
</body>
</html>
