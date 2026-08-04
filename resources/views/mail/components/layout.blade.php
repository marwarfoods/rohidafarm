<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RohidaFarm' }}</title>
    <style>
        body, p, h1, h2, h3, h4, h5, h6 { margin: 0; padding: 0; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f5f7; color: #333333; line-height: 1.6; -webkit-font-smoothing: antialiased; }
        .email-wrapper { width: 100%; background-color: #f4f5f7; padding: 40px 20px; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .email-header { background-color: #ffffff; padding: 25px; text-align: center; border-bottom: 3px solid #1a4f3b; }
        .email-header img { height: 50px; max-width: 100%; object-fit: contain; }
        .email-body { padding: 40px 30px; }
        .email-footer { background-color: #fafafa; padding: 25px; text-align: center; font-size: 13px; color: #888888; border-top: 1px solid #eeeeee; }
        .btn { display: inline-block; background-color: #1a4f3b; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: bold; margin: 20px 0; letter-spacing: 0.5px; text-transform: uppercase; font-size: 14px; }
        .text-center { text-align: center; }
        .order-table { width: 100%; border-collapse: collapse; margin: 25px 0; }
        .order-table th { background-color: #f8f9fa; text-align: left; padding: 12px; font-size: 14px; color: #555; border-bottom: 2px solid #ece7dd; text-transform: uppercase; letter-spacing: 0.5px; }
        .order-table td { padding: 15px 12px; font-size: 15px; border-bottom: 1px solid #ece7dd; }
        .totals-table { width: 100%; max-width: 320px; margin-left: auto; margin-bottom: 30px; }
        .totals-table td { padding: 8px 12px; font-size: 15px; text-align: right; }
        .total-row { font-weight: bold; font-size: 18px; color: #1a4f3b; border-top: 2px solid #ece7dd; }
        .info-box { background-color: #fcfcfc; border: 1px solid #ece7dd; border-radius: 6px; padding: 20px; margin-bottom: 25px; }
    </style>
</head>
<body style="background-color: #f4f5f7; margin: 0; padding: 0;">
    <div class="email-wrapper" style="background-color: #f4f5f7; padding: 40px 20px;">
        <table class="email-container" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <!-- Header -->
            <tr>
                <td class="email-header" style="background-color: #ffffff; padding: 25px; text-align: center; border-bottom: 3px solid #1a4f3b;">
                    <a href="{{ config('app.url') }}" style="display:inline-block; text-decoration:none;">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'RohidaFarm') }}" style="height: 55px; object-fit: contain;">
                    </a>
                </td>
            </tr>
            
            <!-- Body Content -->
            <tr>
                <td class="email-body" style="padding: 40px 30px;">
                    @yield('content')
                </td>
            </tr>
            
            <!-- Footer -->
            <tr>
                <td class="email-footer" style="background-color: #fafafa; padding: 25px; text-align: center; font-size: 13px; color: #888888; border-top: 1px solid #eeeeee;">
                    <p style="margin-bottom: 5px;">&copy; {{ date('Y') }} {{ config('app.name', 'RohidaFarm') }}. All rights reserved.</p>
                    <p style="margin: 0;">Pure & Traditional • Handcrafted for your health.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
