<!DOCTYPE html>
<html>
<head>
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
    </style>
</head>
<body>
    <!-- Store Info -->
    <div class="text-center mb-2">
        <div class="store-name font-bold mb-1"><?php echo htmlspecialchars($settings->store_name); ?></div>
        <div class="mb-1"><?php echo nl2br(htmlspecialchars($settings->address)); ?></div>
        <div class="mb-2"><?php echo htmlspecialchars($settings->phone); ?></div>
    </div>

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
            <td class="item-name"><?php echo htmlspecialchars($item->name); ?></td>
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

    <!-- Footer -->
    <div class="text-center py-1 border-top">
        <p class="mb-1">Thank you for your purchase!</p>
        <p class="mb-1">Please come again</p>
    </div>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px;">Print Receipt</button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
