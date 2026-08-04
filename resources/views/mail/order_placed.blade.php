@extends('mail.components.layout', ['title' => 'Order Confirmed - ' . $order->order_number])

@section('content')
<div class="text-center" style="margin-bottom: 30px;">
    <h1 style="color: #1a4f3b; font-size: 26px; margin-bottom: 10px; font-family: 'Georgia', serif;">Order Confirmed!</h1>
    <p style="color: #666; font-size: 16px;">Hi {{ $order->user->name ?? $order->shipping_name }},</p>
    <p style="color: #444; font-size: 15px; max-width: 450px; margin: 10px auto;">
        Thank you for choosing RohidaFarm. We have received your order <strong>#{{ $order->order_number }}</strong> and are preparing it for shipment.
    </p>
</div>

<table class="order-table" role="presentation">
    <thead>
        <tr>
            <th>Item</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>
                <strong style="color: #333; font-size: 15px;">{{ $item->product_name }}</strong><br>
                @if($item->variant_name)
                <span style="color: #777; font-size: 13px;">Variant: {{ $item->variant_name }}</span>
                @endif
            </td>
            <td class="text-center" style="text-align: center; color: #555;">{{ $item->quantity }}</td>
            <td class="text-right" style="color: #333; font-weight: 500;">₹{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals-table" role="presentation">
    <tr>
        <td style="color: #666;">Subtotal:</td>
        <td style="color: #333; font-weight: 500;">₹{{ number_format($order->subtotal, 2) }}</td>
    </tr>
    @if($order->shipping_charges > 0)
    <tr>
        <td style="color: #666;">Shipping:</td>
        <td style="color: #333; font-weight: 500;">₹{{ number_format($order->shipping_charges, 2) }}</td>
    </tr>
    @endif
    @if($order->discount_amount > 0)
    <tr>
        <td style="color: #198754;">Discount:</td>
        <td style="color: #198754; font-weight: 500;">-₹{{ number_format($order->discount_amount, 2) }}</td>
    </tr>
    @endif
    <tr class="total-row">
        <td>Total:</td>
        <td>₹{{ number_format($order->total, 2) }}</td>
    </tr>
</table>

<div class="info-box">
    <h3 style="font-size: 16px; margin-bottom: 12px; color: #1a4f3b; border-bottom: 1px solid #ece7dd; padding-bottom: 8px;">Shipping Details</h3>
    <p style="font-size: 14px; margin: 0; color: #555; line-height: 1.6;">
        <strong>{{ $order->shipping_name }}</strong><br>
        {{ $order->shipping_address_line1 }}<br>
        @if($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
        <span style="display:inline-block; margin-top:5px; color: #666;">Phone: {{ $order->shipping_phone }}</span>
    </p>
</div>

<div class="text-center" style="margin-bottom: 25px;">
    <p style="font-size: 14px; color: #555; margin-bottom: 5px;">
        Payment Method: <strong>{{ strtoupper($order->payment_method) }}</strong> ({{ ucfirst($order->payment_status) }})
    </p>
</div>

<div class="text-center" style="margin-top: 30px;">
    <a href="{{ route('order.receipt', $order->uuid) }}" class="btn" style="background-color: #1a4f3b; color: #ffffff; padding: 14px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 14px; display: inline-block;">Download PDF Receipt</a>
</div>
@endsection
