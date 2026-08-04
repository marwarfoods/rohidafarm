@extends('mail.components.layout', ['title' => 'Order Status Update - ' . $order->order_number])

@section('content')
<h1 class="email-title">Order Status Update</h1>
<p class="email-text">Hi {{ $order->user->name ?? $order->shipping_name }},</p>
<p class="email-text">There is an update regarding your order <strong>#{{ $order->order_number }}</strong>.</p>

<div class="bg-light p-3 mb-3 text-center">
    <h3 style="font-size: 18px; margin-bottom: 5px; color: #1a4f3b;">Status: {{ ucfirst($order->status) }}</h3>
    <p class="email-text" style="margin-bottom: 0;">{{ $statusMessage }}</p>
</div>

@if($order->status === 'cancelled')
<p class="email-text text-danger">If you have already paid for this order, your refund will be processed back to your original payment method or wallet.</p>
@endif

@if($order->status === 'delivered' || $order->status === 'completed')
<div class="mt-4 mb-4 p-4 rounded" style="background-color: #f0fdf4; border: 1px solid #dcfce7; text-align: center;">
    <h3 style="color: #166534; font-size: 16px; margin-top: 0;">We hope you love your purchase!</h3>
    <p style="color: #166534; font-size: 14px;">Please take a moment to give us a rating. Your feedback helps us improve and serve you better.</p>
    
    <div style="margin-top: 15px;">
        @foreach($order->items as $item)
            @php
                $product = \App\Models\Product::find($item->product_id);
            @endphp
            @if($product)
                <div style="margin-bottom: 10px;">
                    <a href="{{ route('product.show', $product->slug) }}#ratings-reviews" style="display: inline-block; background-color: #16a34a; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; font-size: 14px;">
                        Rate {{ $product->name }}
                    </a>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif

<div class="text-center mt-3">
    <a href="{{ route('order.receipt', $order->uuid) }}" class="btn">View Order Details</a>
</div>
@endsection
