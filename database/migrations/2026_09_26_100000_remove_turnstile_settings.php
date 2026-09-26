<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cloudflare Turnstile was replaced by Google reCAPTCHA v3 — drop its leftover settings.
     */
    public function up(): void
    {
        $keys = DB::table('settings')->where('key', 'like', 'turnstile\_%')->pluck('key');

        DB::table('settings')->whereIn('key', $keys)->delete();

        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
    }

    public function down(): void
    {
        // Turnstile is gone for good; nothing to restore.
    }
};
