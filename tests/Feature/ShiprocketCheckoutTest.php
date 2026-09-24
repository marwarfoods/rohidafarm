<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\ShiprocketCheckoutOrder;
use App\Models\ShiprocketCheckoutVariant;
use App\Models\User;
use App\Services\ShiprocketCheckoutService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The app migrations are MySQL-specific, so these tests need a MySQL test database:
 *   DB_CONNECTION=mysql DB_DATABASE=rohidafarm_srtest php artisan migrate
 *   DB_CONNECTION=mysql DB_DATABASE=rohidafarm_srtest php artisan test --filter=ShiprocketCheckout
 */
class ShiprocketCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    private const API_KEY = 'test-api-key-123';
    private const SECRET = 'test-secret';

    private Category $category;
    private Product $simple;
    private Product $withVariants;
    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('Shiprocket Checkout tests need the MySQL test database (see class docblock).');
        }

        Mail::fake();

        $this->category = Category::create(['name' => 'Ghee', 'slug' => 'ghee-' . Str::random(5), 'description' => 'Pure ghee', 'is_active' => true]);
        $this->simple = Product::create([
            'category_id' => $this->category->id, 'name' => 'Honey', 'slug' => 'honey-' . Str::random(5), 'sku' => 'HNY',
            'weight' => '500 g', 'stock' => 20, 'mrp' => 400, 'sale_price' => 350, 'is_active' => true, 'description' => '<p>Raw honey</p>',
        ]);
        $this->withVariants = Product::create([
            'category_id' => $this->category->id, 'name' => 'A2 Ghee', 'slug' => 'a2-ghee-' . Str::random(5), 'sku' => 'GHEE',
            'weight' => '1L', 'stock' => 0, 'mrp' => 1500, 'sale_price' => 1200, 'is_active' => true,
        ]);
        $this->variant = ProductVariant::create([
            'product_id' => $this->withVariants->id, 'name' => '1 Litre', 'sku' => 'GHEE-1L', 'weight' => '1L', 'stock' => 5, 'mrp' => 1500, 'sale_price' => 1200,
        ]);
    }

    private function configure(bool $enabled = true): void
    {
        Setting::set('shiprocket_checkout_enabled', $enabled ? 'true' : 'false', 'boolean', 'shiprocket_checkout');
        Setting::set('shiprocket_checkout_api_key', Crypt::encryptString(self::API_KEY), 'encrypted', 'shiprocket_checkout');
        Setting::set('shiprocket_checkout_secret_key', Crypt::encryptString(self::SECRET), 'encrypted', 'shiprocket_checkout');
        Setting::set('shiprocket_checkout_environment', 'production', 'string', 'shiprocket_checkout');
        Setting::set('shiprocket_enabled', 'false', 'boolean', 'shipping'); // Shipping off unless a test enables it
    }

    private function details(string $orderId, int $variantId, string $status = 'SUCCESS', string $paymentType = 'CASH_ON_DELIVERY'): array
    {
        return [
            'order_id' => $orderId,
            'cart_data' => ['items' => [['variant_id' => (string) $variantId, 'quantity' => 2]]],
            'status' => $status,
            'phone' => '9876543210',
            'email' => 'buyer' . Str::random(4) . '@example.com',
            'shipping_address' => [
                'phone' => '9876543210', 'line1' => '12 MG Road', 'line2' => 'Near Park', 'city' => 'Jaipur', 'pincode' => '302001',
                'state' => 'Rajasthan', 'country' => 'India', 'landmark' => null, 'first_name' => 'Asha', 'last_name' => 'Verma', 'email' => null,
            ],
            'shipping_charges' => 0,
            'payment_type' => $paymentType,
            'payment_status' => $paymentType === 'PREPAID' ? 'Success' : 'Pending',
            'payments' => $paymentType === 'PREPAID' ? [[
                'txn_id' => 'T1', 'payment_status' => 'Success', 'gateway' => 'Razorpay', 'payment_method' => 'UPI',
                'amount' => 700.0, 'pg_transaction_id' => 'order_X', 'amount_received' => 700.0, 'created_at' => '2025-06-30T06:52:21Z',
            ]] : null,
            'coupon_codes' => null,
            'total_discount' => 0,
            'cod_charges' => null,
            'subtotal_price' => 700,
            'total_amount_payable' => 700.0,
            'platform_order_id' => $orderId,
            'fastrr_order_id' => '113535525',
            'edd' => '2025-07-03',
        ];
    }

    // ── Authentication / HMAC ────────────────────────────────────────────

    public function test_hmac_is_base64_sha256_of_exact_body(): void
    {
        // Independently computed with Python's hmac module.
        $body = '{"order_id":"abc","timestamp":"2024-01-11T14:59:36.603Z"}';
        $this->assertSame('wQWhKaUG6fjaK4NqwKkJ/RNMlF0vGGbcvgpvCv8U44E=', app(ShiprocketCheckoutService::class)->hmac($body, 'test-secret'));
    }

    public function test_requests_carry_api_key_and_hmac_of_sent_body(): void
    {
        $this->configure();
        Http::fake(['checkout-api.shiprocket.com/*' => Http::response(['ok' => true, 'result' => ['total' => 3]])]);

        $result = app(ShiprocketCheckoutService::class)->testConnection();

        $this->assertTrue($result['ok']);
        $this->assertSame('✓ Shiprocket Checkout connection successful.', $result['message']);
        Http::assertSent(function (HttpRequest $r) {
            return $r->url() === 'https://checkout-api.shiprocket.com/api/v1/custom-platform-order/details/list'
                && $r->header('X-Api-Key')[0] === self::API_KEY
                && $r->header('X-Api-HMAC-SHA256')[0] === base64_encode(hash_hmac('sha256', $r->body(), self::SECRET, true))
                && isset($r->data()['startDate'], $r->data()['endDate'], $r->data()['timestamp']);
        });
        $this->assertTrue(Setting::get('shiprocket_checkout_last_test_ok'));
    }

    public function test_failed_connection_reports_safe_error(): void
    {
        $this->configure();
        Http::fake(['*' => Http::response(['ok' => false, 'error' => 'invalid hmac'], 511)]);

        $result = app(ShiprocketCheckoutService::class)->testConnection();

        $this->assertFalse($result['ok']);
        $this->assertStringStartsWith('✗ Connection failed.', $result['message']);
        $this->assertStringContainsString('HTTP 511', $result['message']);
        $this->assertStringNotContainsString(self::SECRET, $result['message']);
        $this->assertStringNotContainsString(self::SECRET, (string) Setting::get('shiprocket_checkout_last_error'));
    }

    public function test_staging_environment_uses_staging_host(): void
    {
        $this->configure();
        Setting::set('shiprocket_checkout_environment', 'staging', 'string', 'shiprocket_checkout');
        Http::fake(['*' => Http::response(['ok' => true, 'result' => ['total' => 0]])]);

        app(ShiprocketCheckoutService::class)->testConnection();

        Http::assertSent(fn (HttpRequest $r) => str_starts_with($r->url(), 'https://fastrr-api-dev.pickrr.com/'));
    }

    // ── Catalog APIs ─────────────────────────────────────────────────────

    public function test_fetch_products_matches_documented_structure(): void
    {
        $res = $this->getJson(route('shiprocket-checkout.catalog.collection-products', ['collection_id' => $this->category->id, 'page' => 1, 'limit' => 100]));

        $res->assertOk()->assertJsonPath('data.total', 2);
        $products = collect($res->json('data.products'))->keyBy('id');

        $productKeys = ['id', 'title', 'body_html', 'vendor', 'product_type', 'created_at', 'handle', 'updated_at', 'tags', 'status', 'variants', 'image', 'options'];
        $variantKeys = ['id', 'title', 'price', 'compare_at_price', 'sku', 'quantity', 'created_at', 'updated_at', 'taxable', 'option_values', 'grams', 'image', 'weight', 'weight_unit'];

        foreach ($products as $p) {
            $this->assertEqualsCanonicalizing($productKeys, array_keys($p));
            $this->assertIsInt($p['id']);
            foreach ($p['variants'] as $v) {
                $this->assertEqualsCanonicalizing($variantKeys, array_keys($v));
                $this->assertIsInt($v['id']);
                $this->assertArrayHasKey('src', $v['image']);
            }
        }

        // Product without variants → one default variant mapped to it.
        $simple = $products[$this->simple->id];
        $this->assertCount(1, $simple['variants']);
        $this->assertSame('350.00', $simple['variants'][0]['price']);
        $this->assertSame(20, $simple['variants'][0]['quantity']);
        $this->assertSame(500, $simple['variants'][0]['grams']);
        $this->assertSame(ShiprocketCheckoutVariant::idFor($this->simple->id, null), $simple['variants'][0]['id']);

        // Product with variants → variant stock/price, stable mapped id.
        $ghee = $products[$this->withVariants->id];
        $this->assertSame(5, $ghee['variants'][0]['quantity']);
        $this->assertSame(ShiprocketCheckoutVariant::idFor($this->withVariants->id, $this->variant->id), $ghee['variants'][0]['id']);
        $this->assertNotSame($simple['variants'][0]['id'], $ghee['variants'][0]['id']);
    }

    public function test_fetch_collections_and_pagination(): void
    {
        $res = $this->getJson(route('shiprocket-checkout.catalog.collections', ['page' => 1, 'limit' => 250]));
        $res->assertOk();
        $this->assertGreaterThanOrEqual(1, $res->json('data.total'));
        $row = collect($res->json('data.collections'))->firstWhere('id', $this->category->id);
        $this->assertEqualsCanonicalizing(['id', 'updated_at', 'body_html', 'handle', 'image', 'title', 'created_at'], array_keys($row));

        $this->getJson(route('shiprocket-checkout.catalog.products', ['limit' => 1]))->assertOk()->assertJsonCount(1, 'data.products');
        $this->getJson(route('shiprocket-checkout.catalog.products', ['limit' => 999]))->assertStatus(422);
        $this->getJson(route('shiprocket-checkout.catalog.collection-products'))->assertStatus(422);
    }

    // ── Access token / launch ────────────────────────────────────────────

    public function test_token_disabled_returns_native_fallback(): void
    {
        $this->configure(false);
        Http::fake();

        $this->postJson(route('shiprocket-checkout.token'), ['source' => 'buy_now', 'product_id' => $this->simple->id, 'quantity' => 1])
            ->assertStatus(409)
            ->assertJsonPath('ok', false)
            ->assertJsonStructure(['fallback_url']);

        Http::assertNothingSent();
    }

    public function test_buy_now_token_uses_mapped_variant_and_redirect_url(): void
    {
        $this->configure();
        Http::fake(['*/api/v1/access-token/checkout' => Http::response([
            'ok' => true, 'result' => ['token' => 'tok_abc', 'expires_at' => '2030-01-01T00:00:00Z', 'data' => ['order_id' => 'chk_001']], 'error' => null,
        ])]);

        $res = $this->postJson(route('shiprocket-checkout.token'), [
            'source' => 'buy_now', 'product_id' => $this->withVariants->id, 'variant_id' => $this->variant->id, 'quantity' => 2,
        ]);

        $res->assertOk()->assertJsonPath('token', 'tok_abc');
        $this->assertStringNotContainsString(self::SECRET, $res->getContent());
        $this->assertStringNotContainsString(self::API_KEY, $res->getContent());

        $expectedVariant = (string) ShiprocketCheckoutVariant::idFor($this->withVariants->id, $this->variant->id);
        Http::assertSent(fn (HttpRequest $r) => $r['cart_data']['items'] === [['variant_id' => $expectedVariant, 'quantity' => 2]]
            && $r['redirect_url'] === route('shiprocket-checkout.return')
            && !empty($r['timestamp']));

        $this->assertDatabaseHas('shiprocket_checkout_orders', ['checkout_order_id' => 'chk_001', 'source' => 'buy_now', 'status' => 'CREATED']);
    }

    public function test_token_api_failure_falls_back(): void
    {
        $this->configure();
        Http::fake(['*' => Http::response(['ok' => false, 'error' => 'down'], 500)]);

        $this->postJson(route('shiprocket-checkout.token'), ['source' => 'buy_now', 'product_id' => $this->simple->id, 'quantity' => 1])
            ->assertStatus(502)
            ->assertJsonPath('ok', false)
            ->assertJson(fn ($json) => $json->where('fallback_url', fn ($url) => str_contains($url, '/shiprocket-checkout/fallback'))->etc());
    }

    // ── Order webhook ────────────────────────────────────────────────────

    public function test_webhook_rejects_wrong_token(): void
    {
        $this->configure();
        app(ShiprocketCheckoutService::class)->webhookToken();

        $this->postJson('/api/fastrr/webhook/order/' . str_repeat('x', 40), ['order_id' => 'abc'])->assertNotFound();
    }

    public function test_webhook_verifies_and_creates_single_order(): void
    {
        $this->configure();
        $vid = ShiprocketCheckoutVariant::idFor($this->simple->id, null);
        $details = $this->details('chk_web_1', $vid, 'SUCCESS', 'PREPAID');
        Http::fake(['*/api/v1/custom-platform-order/details' => Http::response(['ok' => true, 'result' => $details])]);

        $url = app(ShiprocketCheckoutService::class)->webhookUrl();
        // The webhook payload is only a hint — tampered totals must be ignored.
        $payload = array_merge($details, ['total_amount_payable' => 1.0]);

        $this->postJson($url, $payload)->assertOk()->assertJsonPath('order_created', true);
        $this->postJson($url, $payload)->assertOk(); // duplicate delivery

        $record = ShiprocketCheckoutOrder::where('checkout_order_id', 'chk_web_1')->first();
        $this->assertNotNull($record->order_id);
        $this->assertSame(1, Order::whereKey($record->order_id)->count());

        $order = Order::with(['items', 'payments', 'user'])->find($record->order_id);
        $this->assertSame('700.00', $order->total);
        $this->assertSame('shiprocket_checkout', $order->payment_method);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('Asha Verma', $order->shipping_name);
        $this->assertSame('302001', $order->shipping_postal_code);
        $this->assertCount(1, $order->items);
        $this->assertSame(2, $order->items[0]->quantity);
        $this->assertSame('order_X', $order->payments[0]->transaction_id);
        $this->assertSame(strtolower($details['email']), $order->user->email);
        $this->assertSame(18, $this->simple->fresh()->stock);
        // Order Details was called once (the duplicate webhook didn't re-verify)...
        $this->assertSame(1, Http::recorded(fn (HttpRequest $r) => str_ends_with($r->url(), '/custom-platform-order/details'))->count());
        // ...and the new stock was pushed back to Shiprocket via the documented product webhook.
        Http::assertSent(fn (HttpRequest $r) => str_ends_with($r->url(), '/wh/v1/custom/product')
            && $r['id'] === $this->simple->id && $r['variants'][0]['quantity'] === 18);
    }

    public function test_cod_order_is_pushed_to_existing_shiprocket_shipping(): void
    {
        $this->configure();
        Setting::set('shiprocket_enabled', 'true', 'boolean', 'shipping');
        Setting::set('shiprocket_email', 'ship@example.com', 'string', 'shipping');
        Setting::set('shiprocket_password', 'pw', 'string', 'shipping');

        $vid = ShiprocketCheckoutVariant::idFor($this->withVariants->id, $this->variant->id);
        Http::fake([
            '*/api/v1/custom-platform-order/details' => Http::response(['ok' => true, 'result' => $this->details('chk_cod_1', $vid)]),
            'apiv2.shiprocket.in/v1/external/auth/login' => Http::response(['token' => 'ship-token']),
            'apiv2.shiprocket.in/v1/external/orders/create/adhoc' => Http::response(['order_id' => 999, 'shipment_id' => 888, 'status' => 'NEW']),
        ]);

        $this->postJson(app(ShiprocketCheckoutService::class)->webhookUrl(), ['order_id' => 'chk_cod_1'])->assertOk();

        $order = Order::find(ShiprocketCheckoutOrder::where('checkout_order_id', 'chk_cod_1')->value('order_id'));
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame($this->variant->id, $order->items[0]->variant_id);
        $this->assertSame(3, $this->variant->fresh()->stock);

        Http::assertSent(fn (HttpRequest $r) => str_ends_with($r->url(), '/orders/create/adhoc')
            && $r['payment_method'] === 'COD' && $r['order_id'] === $order->order_number);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'delhivery_order_id' => '999']);
    }

    public function test_non_success_status_creates_no_order(): void
    {
        $this->configure();
        $vid = ShiprocketCheckoutVariant::idFor($this->simple->id, null);
        Http::fake(['*' => Http::response(['ok' => true, 'result' => $this->details('chk_fail', $vid, 'FAILED')])]);

        $this->postJson(app(ShiprocketCheckoutService::class)->webhookUrl(), ['order_id' => 'chk_fail'])
            ->assertOk()->assertJsonPath('order_created', false);

        $this->assertDatabaseHas('shiprocket_checkout_orders', ['checkout_order_id' => 'chk_fail', 'status' => 'FAILED', 'order_id' => null]);
        $this->assertSame(20, $this->simple->fresh()->stock);
    }

    // ── Redirect back ────────────────────────────────────────────────────

    public function test_return_redirect_creates_order_and_logs_in_same_browser(): void
    {
        $this->configure();
        $vid = ShiprocketCheckoutVariant::idFor($this->simple->id, null);
        ShiprocketCheckoutOrder::create(['checkout_order_id' => 'chk_ret', 'browser_token' => 'browser-1', 'source' => 'buy_now', 'status' => 'CREATED']);
        Http::fake(['*' => Http::response(['ok' => true, 'result' => $this->details('chk_ret', $vid)])]);

        $res = $this->withSession(['shiprocket_checkout_browser' => 'browser-1'])
            ->get(route('shiprocket-checkout.return', ['oid' => 'chk_ret', 'ost' => 'SUCCESS']));

        $order = Order::find(ShiprocketCheckoutOrder::where('checkout_order_id', 'chk_ret')->value('order_id'));
        $res->assertRedirect(route('checkout.success', $order->uuid));
        $this->assertAuthenticatedAs($order->user);
    }

    public function test_return_from_other_browser_does_not_log_in(): void
    {
        $this->configure();
        $vid = ShiprocketCheckoutVariant::idFor($this->simple->id, null);
        ShiprocketCheckoutOrder::create(['checkout_order_id' => 'chk_ret2', 'browser_token' => 'browser-1', 'source' => 'buy_now', 'status' => 'CREATED']);
        Http::fake(['*' => Http::response(['ok' => true, 'result' => $this->details('chk_ret2', $vid)])]);

        $this->withSession(['shiprocket_checkout_browser' => 'someone-else'])
            ->get(route('shiprocket-checkout.return', ['oid' => 'chk_ret2', 'ost' => 'SUCCESS']))
            ->assertRedirect(route('home'));
        $this->assertGuest();
    }

    // ── Admin settings ───────────────────────────────────────────────────

    public function test_admin_saves_keys_encrypted_and_blank_keeps_them(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin' . Str::random(5) . '@example.com', 'password' => bcrypt('x'), 'role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.settings.save'), ['settings' => [
            'shiprocket_checkout_enabled' => 'true',
            'shiprocket_checkout_api_key' => self::API_KEY,
            'shiprocket_checkout_secret_key' => self::SECRET,
            'shiprocket_checkout_environment' => 'staging',
        ]])->assertRedirect();

        $raw = Setting::where('key', 'shiprocket_checkout_secret_key')->value('value');
        $this->assertNotSame(self::SECRET, $raw);
        $this->assertSame(self::SECRET, Crypt::decryptString($raw));

        // Blank submit keeps the saved secret.
        $this->actingAs($admin)->post(route('admin.settings.save'), ['settings' => ['shiprocket_checkout_secret_key' => '']]);
        $this->assertSame(self::SECRET, Crypt::decryptString(Setting::where('key', 'shiprocket_checkout_secret_key')->value('value')));

        // Invalid values are rejected.
        $this->actingAs($admin)->post(route('admin.settings.save'), ['settings' => ['shiprocket_checkout_base_url' => 'http://evil.test']])
            ->assertSessionHasErrors('settings.shiprocket_checkout_base_url');

        // The settings page never renders the secret.
        $page = $this->actingAs($admin)->get(route('admin.settings.index'));
        $page->assertOk()->assertSee('Shiprocket Checkout')->assertDontSee(self::SECRET)->assertDontSee(self::API_KEY);
    }
}
