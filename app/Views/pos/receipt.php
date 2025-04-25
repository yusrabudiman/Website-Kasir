<!DOCTYPE html>
<html>
<head>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- QR Code library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <meta charset="utf-8">
    <title>Receipt #<?php echo $order->invoice_number; ?></title>
    <style>
        @page {
            margin: 0;
        }
        html, body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 120vh;
            margin: -20px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            background-color:rgb(26, 26, 26);
        }
        .receipt-wrapper {
            width: 100%;
            max-width: 90mm;
            display: flex;
            justify-content: center;
        }
        .receipt-container {
            width: 100%;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .border-top { 
            border-top: 1px dashed #000;
            margin-top: 8px;
            padding-top: 8px;
        }
        .border-bottom { 
            border-bottom: 1px dashed #000;
            margin-bottom: 8px;
            padding-bottom: 8px;
        }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .store-name { 
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        table { 
            width: 100%;
            border-collapse: collapse;
        }
        th, td { 
            padding: 4px 0;
            vertical-align: top;
        }
        .item-name { 
            max-width: 160px;
            line-height: 1.2;
        }
        .qr-code {
            text-align: center;
            margin: 15px 0;
        }
        .qr-code img {
            margin: 0 auto;
        }
        .social-info {
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
            color: #666;
        }
        .stock-info {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .summary-table td {
            padding: 4px 0;
        }
        .total-row {
            font-size: 14px;
            font-weight: bold;
        }
        @media print {
            html, body {
              
                display: block;
                background-color: white;
                padding: 0;
                margin: 0;
            }
            .receipt-wrapper {
                max-width: none;
            }
            .receipt-container {
                box-shadow: none;
                padding: 10px;
                margin: 0;
            }
            .no-print { 
                display: none; 
            }
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        <div class="receipt-container">
            <!-- Store Info -->
            <div class="text-center mb-3">
                <?php if (isset($settings->logo) && !empty($settings->logo)): ?>
                    <img src="<?php echo $settings->logo; ?>" alt="Store Logo" class="mx-auto mb-2" style="max-width: 100px;">
                <?php endif; ?>
                <div class="store-name"><?php echo htmlspecialchars($settings->store_name); ?></div>
                <div class="mb-1"><?php echo nl2br(htmlspecialchars($settings->address)); ?></div>
                <div class="mb-2"><?php echo htmlspecialchars($settings->phone); ?></div>
            </div>

            <!-- QR Code -->
            <div class="qr-code" id="qrcode"></div>

            <!-- Order Info -->
            <div class="border-bottom border-top py-1">
                <table>
                    <tr>
                        <td width="40%">Invoice</td>
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
                    <th class="text-right" width="15%">Buy</th>
                    <th class="text-right" width="25%">Price</th>
                    <th class="text-right" width="25%">Total</th>
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
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($item->price, 0, ',', '.'); ?></td>
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($item->subtotal, 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <!-- Order Summary -->
            <table class="summary-table py-1">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($order->total_amount, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Tax (<?php echo $settings->tax_rate; ?>%)</td>
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($order->tax_amount, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Service Charge (<?php echo $settings->service_charge; ?>%)</td>
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($order->service_charge, 0, ',', '.'); ?></td>
                </tr>
                <tr class="total-row border-top py-1">
                    <td>Total</td>
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($order->final_amount, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Payment</td>
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($order->payment_amount, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Change</td>
                    <td class="text-right"><?php echo $currencySymbol; ?><?php echo number_format($order->change_amount, 0, ',', '.'); ?></td>
                </tr>
            </table>

            <!-- Enhanced Footer -->
            <div class="text-center py-1 border-top">
                <p class="mb-2" style="font-size: 13px;"><?php echo htmlspecialchars($settings->thank_you_message); ?></p>
                <div class="social-info">
                    <p class="mb-1">Business Hours: 09:00 - 22:00</p>
                    <p class="mb-1">Follow us on:</p>
                    <p class="mb-1">Instagram: @yourstorename</p>
                    <p>Website: www.yourstore.com</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print text-center" style="position: fixed; bottom: 20px; left: 0; right: 0;">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200">
            <i class="fas fa-print mr-2"></i> Print Receipt
        </button>
        <button onclick="window.location.href='/pos'" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded ml-2 transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to POS
        </button>
    </div>

    <script>
        // Generate QR Code
        window.onload = function() {
            var qr = qrcode(0, 'M');
            qr.addData('<?php echo $order->invoice_number; ?>');
            qr.make();
            document.getElementById('qrcode').innerHTML = qr.createImgTag(6);
            
            // Auto print
            window.print();
        }
    </script>
</body>
</html>
