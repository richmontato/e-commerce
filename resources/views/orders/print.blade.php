<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <h1>Invoice</h1>
                                <strong>Order #:</strong> {{ $order->id }}<br>
                                <strong>Created:</strong> {{ $order->created_at->toFormattedDateString() }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Billed To:</strong><br>
                                {{ $order->user->name }}<br>
                                {{ $order->user->email }}
                            </td>
                            
                            {{-- This is the new shipping address section --}}
                            <td class="text-right">
                                @if($order->shipping_address)
                                    <strong>Shipped To:</strong><br>
                                    <div style="white-space: pre-line;">{{ $order->shipping_address }}</div>
                                @endif
                                <div style="margin-top: 12px;">
                                    <strong>Shipping Method:</strong><br>
                                    {{ $order->shipping_method_label }}
                                </div>
                                <div style="margin-top: 12px;">
                                    <strong>Payment Method:</strong><br>
                                    {{ strtoupper(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td class="text-right">Price</td>
            </tr>

            @foreach ($order->items as $item)
                <tr class="item">
                    <td>{{ $item->product->name ?? $item->product_name }} (x{{ $item->qty }})</td>
                    <td class="text-right">Rp. {{ number_format($item->unit_price * $item->qty, 2) }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td></td>
                <td class="text-right">
                   <strong>Total: Rp. {{ number_format($order->total_amount, 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>