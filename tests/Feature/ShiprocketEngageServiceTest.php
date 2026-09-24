<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\ShiprocketEngageService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ShiprocketEngageServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // The app migrations are MySQL-specific, so on SQLite build only the tables this service reads.
        if (Schema::hasTable('settings')) {
            return;
        }
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->string('type')->nullable();
            $t->string('group')->nullable();
            $t->string('description')->nullable();
            $t->timestamps();
        });
        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->string('delhivery_order_id')->nullable();
            $t->timestamps();
        });
    }

    private function fakeShiprocket(int $loginStatus = 200): void
    {
        Http::fake([
            '*/auth/login' => $loginStatus === 200
                ? Http::response(['token' => 'tok', 'company_id' => 42, 'first_name' => 'API', 'last_name' => 'User'])
                : Http::response(['message' => 'Invalid email and password combination'], $loginStatus),
            '*/account/details/wallet-balance' => Http::response(['data' => ['balance_amount' => '150.50']]),
            '*/channels' => Http::response(['data' => [['id' => 76893, 'name' => 'CUSTOM', 'status' => 'Active']]]),
        ]);
    }

    public function test_successful_connection_reports_all_checks(): void
    {
        config(['services.shiprocket_engage.email' => 'api@brand.com', 'services.shiprocket_engage.password' => 'secret']);
        $this->fakeShiprocket();

        $result = app(ShiprocketEngageService::class)->testConnection();

        $this->assertTrue($result['ok']);
        $this->assertSame('env', $result['source']);
        $byLabel = collect($result['checks'])->keyBy('label');
        $this->assertSame('pass', $byLabel['API authentication']['status']);
        $this->assertStringContainsString('Company ID: 42', $byLabel['API authentication']['detail']);
        $this->assertStringContainsString('150.50', $byLabel['Account / wallet access']['detail']);
        $this->assertStringContainsString('CUSTOM #76893', $byLabel['Channels']['detail']);
        $this->assertSame('info', $byLabel['Engage status on orders']['status']);
    }

    public function test_bad_credentials_fail_with_reason(): void
    {
        $this->fakeShiprocket(403);

        $result = app(ShiprocketEngageService::class)->testConnection('wrong@brand.com', 'nope');

        $this->assertFalse($result['ok']);
        $failed = collect($result['checks'])->firstWhere('status', 'fail');
        $this->assertStringContainsString('HTTP 403', $failed['detail']);
        $this->assertStringContainsString('Invalid email and password combination', $failed['detail']);
    }

    public function test_missing_credentials_fail_without_calling_api(): void
    {
        Http::fake();

        $result = app(ShiprocketEngageService::class)->testConnection();

        $this->assertFalse($result['ok']);
        Http::assertNothingSent();
    }

    public function test_credential_priority_and_encrypted_password(): void
    {
        Setting::set('shiprocket_email', 'ship@brand.com');
        Setting::set('shiprocket_password', 'shippass');
        $service = app(ShiprocketEngageService::class);
        $this->assertSame('shipping_settings', $service->credentials()['source']);

        Setting::set('shiprocket_engage_email', 'engage@brand.com');
        Setting::set('shiprocket_engage_password', Crypt::encryptString('engagepass'), 'encrypted');
        $creds = $service->credentials();
        $this->assertSame('engage_settings', $creds['source']);
        $this->assertSame('engagepass', $creds['password']);

        config(['services.shiprocket_engage.email' => 'env@brand.com', 'services.shiprocket_engage.password' => 'envpass']);
        $this->assertSame('env', $service->credentials()['source']);
    }
}
