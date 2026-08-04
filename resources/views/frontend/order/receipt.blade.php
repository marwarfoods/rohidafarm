<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #f3f3f3;
            color: #2b2b2b;
            font-size: 0.9rem;
        }
        .invoice-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            max-width: 850px;
            margin: 40px auto;
            padding: 40px;
            border: 1px solid #ece7dd;
        }
        .font-heading {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 700;
        }
        .text-success-dark {
            color: #1b5e20;
        }
        .table th {
            background-color: #fdfaf5 !important;
            border-bottom: 2px solid #ece7dd !important;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            font-weight: 700;
        }
        .table td {
            border-bottom: 1px solid #ece7dd !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff;
            }
            .invoice-card {
                box-shadow: none;
                margin: 0;
                padding: 20px;
                border: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Controls Menu (Visible only on Web, hidden during printing/PDF export) -->
    <div class="container py-3 d-flex justify-content-between align-items-center no-print" style="max-width: 850px;">
        <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
        <div class="d-flex gap-2">
            <button id="btnPrint" class="btn btn-outline-success rounded-pill px-3 py-2"><i class="bi bi-printer me-1"></i> Print Invoice</button>
            <button id="btnDownloadPdf" class="btn btn-success rounded-pill px-4 py-2"><i class="bi bi-download me-1"></i> Download PDF Receipt</button>
        </div>
    </div>

    <!-- Printable Invoice Container -->
    <div class="invoice-card" id="invoiceContent">
        <!-- Logo and Invoice Header -->
        <div class="row align-items-start mb-5 pb-4 border-bottom" style="border-color: #ece7dd !important;">
            <div class="col-6">
                @if(\App\Models\Setting::get('main_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('main_logo')) }}" alt="RohidaFarm Logo" style="height: 55px; object-fit: contain; margin-bottom: 5px;">
                @else
                    <span class="fs-2 fw-bold text-uppercase text-success-dark font-heading d-block" style="line-height:1;">RohidaFarm</span>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 2px;">Pure and Traditional</span>
                @endif
            </div>
            <div class="col-6 text-end">
                <h2 class="font-heading fw-bold text-uppercase text-success-dark mb-1">Tax Invoice</h2>
                <div style="font-size: 0.85rem;" class="text-muted">
                    <span class="d-block"><strong>Invoice Number:</strong> RF-INV-{{ $order->order_number }}</span>
                    <span class="d-block"><strong>Date of Invoice:</strong> {{ $order->created_at->format('d M Y') }}</span>
                    <span class="d-block"><strong>GSTIN Number:</strong> 27AAAAA1111A1Z1</span>
                </div>
            </div>
        </div>

        <!-- 2-Column Addresses Row -->
        <div class="row g-4 mb-5">
            <!-- Left: Seller Info -->
            <div class="col-6">
                <h6 class="text-uppercase fw-bold text-success-dark font-heading mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">Seller Details</h6>
                <div class="lh-sm" style="font-size: 0.85rem;">
                    <strong class="text-dark d-block mb-1">RohidaFarm Private Limited</strong>
                    <span class="text-muted d-block">Plot 45, Sector B, Kothrud</span>
                    <span class="text-muted d-block">Pune, Maharashtra, 411038</span>
                    <span class="text-muted d-block">GSTIN: 27AAAAA1111A1Z1</span>
                    <span class="text-muted d-block mt-2"><i class="bi bi-envelope me-1"></i> care@rohidafarm.com</span>
                    <span class="text-muted d-block"><i class="bi bi-telephone me-1"></i> +91 98765 43210</span>
                </div>
            </div>
            
            <!-- Right: Shipping / Billing Address -->
            <div class="col-6">
                <h6 class="text-uppercase fw-bold text-success-dark font-heading mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">Bill To / Ship To</h6>
                <div class="lh-sm" style="font-size: 0.85rem;">
                    <strong class="text-dark d-block mb-1">{{ $order->shipping_name }}</strong>
                    <span class="text-muted d-block">{{ $order->shipping_address_line1 }}</span>
                    @if($order->shipping_address_line2)
                        <span class="text-muted d-block">{{ $order->shipping_address_line2 }}</span>
                    @endif
                    <span class="text-muted d-block">{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}</span>
                    <span class="text-muted d-block mt-2"><i class="bi bi-telephone me-1"></i> Phone: {{ $order->shipping_phone }}</span>
                    <span class="text-muted d-block"><i class="bi bi-envelope me-1"></i> Email: {{ $order->user->email }}</span>
                </div>
            </div>
        </div>

        <!-- Product Details Table -->
        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col" style="width: 5%;">#</th>
                        <th scope="col" style="width: 10%;">Product Image</th>
                        <th scope="col" style="width: 45%;">Description</th>
                        <th scope="col" class="text-center" style="width: 10%;">Qty</th>
                        <th scope="col" class="text-end" style="width: 15%;">Unit Price</th>
                        <th scope="col" class="text-end" style="width: 15%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        @php
                            $prodImg = $item->product->primaryImage ? asset($item->product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ $prodImg }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <strong class="text-dark font-heading fs-6">{{ $item->product_name }}</strong>
                                @if($item->variant_name)
                                    <span class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-tag text-success me-1"></i>Variant: {{ $item->variant_name }}</span>
                                @endif
                            </td>
                            <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                            <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-end fw-bold text-success-dark">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calculations Summary -->
        <div class="row justify-content-end mb-5">
            <div class="col-md-5">
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                        <span class="text-muted">Subtotal:</span>
                        <span class="text-dark fw-medium">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-1 text-success" style="font-size: 0.85rem;">
                            <span>Discount ({{ $order->coupon_code }}):</span>
                            <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                        <span class="text-muted">Taxes (5% GST):</span>
                        <span class="text-dark fw-medium">₹{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="font-size: 0.85rem;">
                        <span class="text-muted">Shipping Fee:</span>
                        <span class="text-dark fw-medium">{{ $order->shipping_charges > 0 ? '₹' . number_format($order->shipping_charges, 2) : 'FREE' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong class="font-heading text-dark fs-5">Invoice Total:</strong>
                        <strong class="font-heading text-success-dark fs-4">₹{{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Row -->
        <div class="row align-items-end pt-4 border-top" style="border-color: #ece7dd !important;">
            <!-- Left Side: Thanks & Policy note -->
            <div class="col-7">
                <h6 class="font-heading fw-bold text-success-dark mb-1" style="font-size: 1rem;">Thank you for your purchase!</h6>
                <p class="text-muted m-0" style="font-size: 0.8rem; line-height: 1.5;">
                    By buying from RohidaFarm, you help protect organic farming ecosystems. For policies, view our <a href="{{ route('privacy-policy') }}" class="text-success text-decoration-none fw-semibold">Privacy Policy</a> & <a href="{{ route('refund-policy') }}" class="text-success text-decoration-none fw-semibold">Refund Policy</a>.
                </p>
            </div>
            
            <!-- Right Side: Computer Generated Stamp -->
            <div class="col-5 text-end text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                <p class="mb-2"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                <div class="border d-inline-block p-2 rounded bg-light text-center">
                    <i class="bi bi-shield-fill-check text-success fs-5"></i>
                    <span class="d-block fw-bold" style="font-size: 0.7rem; color: #1b5e20;">Computer Generated</span>
                    <span style="font-size: 0.6rem;">No signature required.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- html2pdf Client side exporter script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Print action
            document.getElementById('btnPrint').addEventListener('click', function () {
                window.print();
            });

            // PDF Download Action
            document.getElementById('btnDownloadPdf').addEventListener('click', function () {
                const element = document.getElementById('invoiceContent');
                const opt = {
                    margin:       10,
                    filename:     'RohidaFarm_Invoice_{{ $order->order_number }}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2 },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                // Generate PDF
                html2pdf().set(opt).from(element).save();
            });
        });
    </script>
</body>
</html>
