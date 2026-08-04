@extends('layouts.app')

@section('content')
<div class="container py-5 text-center" style="min-height: 50vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div class="spinner-border text-success mb-3" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h3 class="font-heading">Initializing Secure Payment...</h3>
    <p class="text-muted">Please do not refresh the page. Waiting for Razorpay Gateway.</p>

    <!-- Form to submit signature back to server -->
    <form action="{{ route('checkout.razorpay.callback') }}" method="POST" id="razorpayCallbackForm" class="d-none">
        @csrf
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        <input type="hidden" name="uuid" value="{{ $order->uuid }}">
    </form>

    <!-- Form to cancel the orphaned order if payment fails or modal is closed -->
    <form action="{{ route('checkout.razorpay.cancel', $order->uuid) }}" method="POST" id="razorpayCancelForm" class="d-none">
        @csrf
    </form>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            "key": "{{ $key }}",
            "amount": "{{ $amount }}",
            "currency": "INR",
            "name": "{{ config('app.name', 'RohidaFarm') }}",
            "description": "Order Payment #{{ $order->order_number }}",
            "image": "{{ asset('assets/images/logo.png') }}",
            "order_id": "{{ $razorpayOrderId }}",
            "handler": function (response) {
                // Set values to form and submit
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('razorpayCallbackForm').submit();
            },
            "prefill": {
                "name": "{{ $user->name }}",
                "email": "{{ $user->email }}",
                "contact": "{{ $user->phone }}"
            },
            "theme": {
                "color": "#198754" // Success green
            },
            "modal": {
                "ondismiss": function() {
                    // Redirect back if user closes modal
                    document.getElementById('razorpayCancelForm').submit();
                }
            }
        };

        var rzp1 = new Razorpay(options);
        
        rzp1.on('payment.failed', function (response){
            alert("Payment Failed: " + response.error.description);
            document.getElementById('razorpayCancelForm').submit();
        });

        // Open modal automatically
        rzp1.open();
    });
</script>
@endpush
