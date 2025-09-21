<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Order #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; margin: 40px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin-bottom: 5px; }
        .info-table, .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .info-table td { padding: 5px; }
        .items-table th, .items-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; }
        .total { text-align: right; margin-top: 20px; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #555; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Hugpong Amigos</h2>
        <p><strong>Order Invoice</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Order ID:</strong> #{{ $order->id }}</td>
            <td><strong>Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Member:</strong> {{ $order->member->full_name }}</td>
            <td><strong>Status:</strong> {{ $order->status }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Cashback</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₱{{ number_format($item->price, 2) }}</td>
                <td>₱{{ number_format($item->cashback * $item->quantity, 2) }}</td>
                <td>₱{{ number_format($item->quantity * $item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount, 2) }}</p>
        <p><strong>Total Cashback Earned:</strong> ₱{{ number_format($order->items->sum(fn($i) => $i->cashback * $i->quantity), 2) }}</p>
    </div>

    <div class="footer">
        Thank you for shopping with Hugpong Amigos!
    </div>

</body>
</html>
