<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Report Summary</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            float: left;
            width: 100px;
        }
        .title {
            text-align: center;
            font-size: 20px;
            margin-top: 10px;
        }
        .summary-badges {
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            background: #f0f0f0;
            color: #333;
            border-radius: 3px;
            padding: 4px 8px;
            margin-right: 5px;
            font-size: 11px;
        }
        .clear { clear: both; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('img/logo.png') }}" class="logo">
        <div class="title">Sales Report</div>
        <div class="clear"></div>
    </div>

    @if(!empty($filters))
        <div class="summary-badges">
            @foreach($filters as $key => $value)
                @if(!empty($value))
                    <span class="badge">
                        {{ ucfirst($key) }}: {{ $value }}
                    </span>
                @endif
            @endforeach
        </div>
    @endif

    <h4>Sales Trends</h4>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chartData['dates'] as $index => $date)
                <tr>
                    <td>{{ $date }}</td>
                    <td>₱{{ number_format($chartData['totals'][$index], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4 style="margin-top:30px;">Order Details</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->member->user->name ?? '-' }}</td>
                    <td>{{ $order->status }}</td>
                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed on {{ now()->format('Y-m-d H:i') }}
    </div>

</body>
</html>
