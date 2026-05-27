<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Pesanan</title>
    <style>
        /* Reset & Base */
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #374151;
            line-height: 1.6;
        }

        /* Layout */
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Header */
        .header {
            background-color: #4f46e5;
            padding: 20px 10px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Content */
        .content {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            color: #111827;
            margin-bottom: 8px;
        }

        .intro-text {
            color: #6b7280;
            margin-bottom: 32px;
        }

        /* Status Badge */
        .status-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .status-badge {
            display: inline-block;
            padding: 12px 32px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        /* Status Colors */
        .status-pending {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .status-paid {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #d1fae5;
        }

        .status-approved {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .status-processing {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .status-awaiting_confirmation {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .status-shipped {
            background-color: #eef2ff;
            color: #4338ca;
            border: 1px solid #e0e7ff;
        }

        .status-completed {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }

        .status-delivered {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }

        .status-cancelled {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }

        /* Order Details Card */
        .order-details {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
            border: 1px solid #f3f4f6;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            border-bottom: 1px dashed #e5e7eb;
            padding-bottom: 12px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            color: #6b7280;
        }

        .detail-value {
            color: #111827;
            font-weight: 600;
            font-family: monospace;
            font-size: 15px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }

        .items-table th {
            text-align: left;
            padding-bottom: 16px;
            color: #9ca3af;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            border-bottom: 2px solid #f3f4f6;
        }

        .items-table td {
            padding: 20px 0;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .item-name {
            color: #111827;
            font-weight: 600;
            display: block;
            font-size: 15px;
        }

        .item-meta {
            color: #6b7280;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .item-price {
            text-align: right;
            color: #374151;
            font-weight: 600;
            white-space: nowrap;
            font-size: 15px;
        }

        /* Total Section */
        .total-section {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .total-label {
            font-size: 16px;
            font-weight: 600;
            color: #4b5563;
        }

        .total-value {
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5;
        }

        /* Message Box */
        .message-box {
            background-color: #eff6ff;
            border-left: 4px solid #4f46e5;
            padding: 20px;
            margin-bottom: 32px;
            font-size: 15px;
            color: #1e40af;
            border-radius: 0 8px 8px 0;
        }

        /* Button */
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 32px 20px;
            color: #9ca3af;
            font-size: 13px;
        }

        .footer p {
            margin: 8px 0;
        }

        .footer a {
            color: #6b7280;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Update Status Pesanan</h1>
            </div>

            <div class="content">
                <div class="greeting">
                    Halo, <strong>{{ $order->recipient_name }}</strong> 👋
                </div>

                <p class="intro-text">
                    Ada pembaruan status terbaru untuk pesanan Anda.
                </p>

                <div class="status-container">
                    <span class="status-badge status-{{ $status }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="order-details">
                    <div class="detail-row">
                        <span class="detail-label">Nomor Pesanan</span>
                        <span class="detail-value">#{{ $order->order_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tanggal Pesanan</span>
                        <span class="detail-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if($order->shipping_tracking_number)
                        <div class="detail-row">
                            <span class="detail-label">Nomor Resi</span>
                            <span class="detail-value">{{ $order->shipping_tracking_number }}</span>
                        </div>
                    @endif
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th style="text-align: right;">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <span class="item-name">{{ $item->product_name }}</span>
                                    <span class="item-meta">
                                        {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                        @if($item->variant_details)
                                            <br>Variasi: {{ $item->variant_details }}
                                        @endif
                                    </span>
                                </td>
                                <td class="item-price">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="total-section">
                    <span class="total-label">Total Pembayaran</span>
                    <span class="total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                @if($status === 'pending')
                    <div class="message-box" style="background-color: #fff7ed; border-color: #f97316; color: #9a3412;">
                        Silakan selesaikan pembayaran Anda sebelum
                        <strong>{{ $order->created_at->addHours(12)->format('d M Y H:i') }}</strong> agar pesanan tidak
                        dibatalkan otomatis.
                    </div>
                @elseif($status === 'paid')
                    <div class="message-box" style="background-color: #ecfdf5; border-color: #10b981; color: #065f46;">
                        Terima kasih! Pembayaran Anda telah kami terima. Pesanan akan segera kami proses untuk pengiriman.
                    </div>
                @elseif($status === 'approved')
                    <div class="message-box">
                        Pesanan Anda telah dikonfirmasi dan akan segera dikirim.
                    </div>
                @elseif($status === 'processing' || $status === 'awaiting_confirmation')
                    <div class="message-box">
                        Pesanan Anda sedang dikemas. Anda akan mendapatkan notifikasi saat pesanan dikirim.
                    </div>
                @elseif($status === 'shipped')
                    <div class="message-box">
                        Pesanan Anda sedang dalam perjalanan! Anda dapat melacak posisi paket menggunakan nomor resi yang
                        tertera di atas.
                    </div>
                @elseif($status === 'completed' || $status === 'delivered')
                    <div class="message-box" style="background-color: #ecfdf5; border-color: #10b981; color: #065f46;">
                        Pesanan telah selesai. Terima kasih telah berbelanja di toko kami! Jangan lupa berikan ulasan.
                    </div>
                @elseif($status === 'cancelled')
                    <div class="message-box" style="border-color: #ef4444; background-color: #fef2f2; color: #991b1b;">
                        Pesanan Anda telah dibatalkan. Jika ini kesalahan, silakan hubungi layanan pelanggan kami.
                    </div>
                @endif

                <div class="btn-container">
                    <a href="{{ url('/order-detail/' . $order->order_number) }}" class="btn">
                        Lihat Detail Pesanan
                    </a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>

</html>