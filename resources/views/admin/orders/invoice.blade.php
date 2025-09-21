<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0 30px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            height: auto;
        }

        .header-details {
            margin-left: 20px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .totals-box {
            margin-top: 20px;
            padding: 10px;
            border: 1px dashed #888;
            width: 300px;
        }

        .totals-box p {
            margin: 4px 0;
            font-weight: bold;
        }

        .proof {
            margin-top: 30px;
        }

        .proof img {
            width: 300px;
            height: auto;
            border: 1px solid #ddd;
        }

        .qr {
            margin-top: 30px;
        }

        .no-print {
            color: #888;
            font-size: 10px;
            text-align: center;
            margin-top: 40px;
        }

        .admin-notes {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
        <div class="header-details">
            <h1>Order Invoice</h1>
            <p>
                <strong>Invoice #:</strong> {{ $order->id }}<br>
                <strong>Date:</strong> {{ $order->created_at->format('Y-m-d') }}
            </p>
        </div>
    </div>

    {{-- Customer Info --}}
    <p>
        <strong>Customer:</strong> {{ $order->member->full_name ?? 'N/A' }}<br>
        <strong>Contact:</strong> {{ $order->member->contact_number ?? 'N/A' }}<br>
        <strong>Delivery Type:</strong> {{ ucfirst($order->delivery_type) }}<br>
        @if($order->delivery_address)
            <strong>Address:</strong> {{ $order->delivery_address }}<br>
        @endif
    </p>

    {{-- Order Items --}}
    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Cashback</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $totalCashback = 0;
            @endphp
            @foreach($order->items as $item)
                @php
                    $lineTotal = $item->price * $item->quantity;
                    $lineCashback = $item->cashback * $item->quantity;
                    $total += $lineTotal;
                    $totalCashback += $lineCashback;
                @endphp
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₱{{ number_format($item->price, 2) }}</td>
                    <td>₱{{ number_format($lineTotal, 2) }}</td>
                    <td>₱{{ number_format($lineCashback, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-box">
        <p>Total Amount: ₱{{ number_format($total, 2) }}</p>
        <p>Total Cashback Earned: ₱{{ number_format($totalCashback, 2) }}</p>
        <p>Status: {{ ucfirst($order->status) }}</p>
    </div>

    {{-- Payment Details --}}
    @if($order->payment_method)
    <div class="summary">
        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
        @if($order->payment_reference)
            <p><strong>Reference:</strong> {{ $order->payment_reference }}</p>
        @endif
    </div>
    @endif

    {{-- Proof of Delivery --}}
    @if($order->proof_photo)
        <div class="proof">
            <strong>Proof of Delivery:</strong><br>
            <img src="{{ public_path('uploads/proof/' . $order->proof_photo) }}" alt="Proof Photo">
        </div>
    @endif

  {{-- QR Code --}}
<div class="qr text-center mt-4">
    <strong>Scan to View Order</strong><br>
    {!! QrCode::size(150)->generate(route('admin.orders.invoice', $order->id)) !!}
</div>


    {{-- Admin Notes --}}
    @if($order->admin_notes)
        <div class="admin-notes">
            <strong>Admin Notes:</strong><br>
            {{ $order->admin_notes }}
        </div>
    @endif

    {{-- Footer --}}
    <p class="no-print">
        Printed on {{ now()->format('Y-m-d H:i') }}
    </p>
</body>
</html>
