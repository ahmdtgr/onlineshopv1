<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            width: 100%;
            margin: 0;
            padding: 15px;
            border: 1px solid #eee;
        }
        .header {
            text-align: left;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 0 0 2px 0;
            font-size: 12px;
            color: #666;
        }
        .invoice-title {
            text-align: center;
            margin: 15px 0 5px 0;
        }
        .invoice-title h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .invoice-details {
            text-align: center;
            margin-bottom: 15px;
        }
        .invoice-details p {
            margin: 2px 0;
        }
        
        /* Grid layout for info sections */
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .grid-row {
            display: table-row;
        }
        
        .grid-cell {
            display: table-cell;
            width: 50%;
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .grid-header {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            color: #666;
            margin: 0 0 5px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .grid-content p {
            margin: 3px 0;
        }
        
        .light-text {
            color: #666;
        }
        
        .bold-text {
            font-weight: bold;
        }
        
        /* Status colors */
        .status-paid {
            color: green;
            font-weight: bold;
        }
        .status-unpaid {
            color: red;
            font-weight: bold;
        }
        
        /* Order items table */
        .detail-heading {
            text-transform: uppercase;
            font-size: 11px;
            color: #666;
            font-weight: bold;
            margin: 15px 0 5px 0;
        }
        
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 11px;
        }
        table.items th, table.items td {
            padding: 5px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table.items th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        
        /* Totals */
        .totals-container {
            margin-top: 10px;
        }
        .totals {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 3px 5px;
            border: none;
        }
        .totals tr.subtotal td,
        .totals tr.shipping td {
            text-align: right;
        }
        .totals tr.total td {
            text-align: right;
            font-weight: bold;
            border-top: 1px solid #ddd;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            color: #777;
            font-size: 10px;
            padding-top: 8px;
            margin-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header with company info -->
        <div class="header">
            <h2>{{ $store->name }}</h2>
            <p>{{ $store->address ?? '' }}</p>
        </div>
        
        <!-- Invoice title and number -->
        <div class="invoice-title">
            <h1>INVOICE</h1>
        </div>
        
        <div class="invoice-details">
            <p><strong>No: {{ $order->order_number }}</strong></p>
            <p>Tanggal: {{ Carbon\Carbon::parse($order->created_at)->format('d M Y') }} | Cetak: {{ $date }}</p>
        </div>
        
        <!-- Info grid layout (2x2) using table layout -->
        <div class="info-grid">
            <div class="grid-row">
                <!-- Customer Info -->
                <div class="grid-cell">
                    <div class="grid-header">INFORMASI PELANGGAN</div>
                    <div class="grid-content">
                        <p class="bold-text">{{ $order->recipient_name }}</p>
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->user->email ?? '-' }}</p>
                        <p>{{ $order->shipping_address }}</p>
                    </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="grid-cell">
                    <div class="grid-header">INFORMASI PENGIRIMAN</div>
                    <div class="grid-content">
                        @php
                            $shippingDetails = $order->shipping_method_detail;
                            // Handle old data that might still be JSON string
                            if (is_string($shippingDetails)) {
                                $shippingDetails = json_decode($shippingDetails, true);
                            }
                            $shippingDetails = $shippingDetails ?? [];
                        @endphp
                        <p><span class="light-text">Kurir:</span> {{ $shippingDetails['courier_name'] ?? '-' }}</p>
                        <p><span class="light-text">Layanan:</span> {{ $shippingDetails['courier_service_name'] ?? $shippingDetails['service'] ?? '-' }}</p>
                        <p><span class="light-text">Estimasi:</span> {{ $shippingDetails['duration'] ?? '-' }}</p>
                        @if($order->shipping_tracking_number)
                        <p><span class="light-text">No. Resi:</span> <strong>{{ $order->shipping_tracking_number }}</strong></p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="grid-row">
                <!-- Payment Status -->
                <div class="grid-cell">
                    <div class="grid-header">STATUS PEMBAYARAN</div>
                    <div class="grid-content">
                        <p><span class="light-text">Status:</span> 
                            @if($order->payment_status == 'paid')
                            <span class="status-paid">LUNAS</span>
                            @else
                            <span class="status-unpaid">BELUM LUNAS</span>
                            @endif
                        </p>
                        <p><span class="light-text">Metode:</span> 
                            @if($order->payment_gateway_transaction_id)
                            Payment Gateway
                            @else
                            Transfer Manual
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="grid-cell">
                    <div class="grid-header">CATATAN</div>
                    <div class="grid-content">
                        @if($order->noted)
                        <p>{{ $order->noted }}</p>
                        @else
                        <p><em>Tidak ada catatan</em></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="detail-heading">DETAIL PRODUK</div>
        <table class="items">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Produk</th>
                    <th width="10%" class="text-center">Jumlah</th>
                    <th width="20%" class="text-right">Harga</th>
                    <th width="20%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variant_name)
                        <br><small>{{ $item->variant_name }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals-container">
            <table class="totals">
                <tr class="subtotal">
                    <td width="80%">Subtotal:</td>
                    <td width="20%">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr class="shipping">
                    <td>Ongkos Kirim:</td>
                    <td>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                <tr class="total">
                    <td>Total:</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas pembelian Anda! Invoice ini merupakan bukti pembayaran yang sah.</p>
        </div>
    </div>
</body>
</html>