<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $data = Cache::rememberForever("setting.{$key}", function () use ($key) {
            $setting = self::where('key', $key)->first();
            return $setting ? [
                'value' => $setting->value,
                'type' => $setting->type
            ] : null;
        });

        if (!$data) {
            return $default;
        }

        return match ($data['type']) {
            'boolean' => filter_var($data['value'], FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($data['value'], true),
            default => $data['value'],
        };
    }

    /**
     * Set/Update a setting key.
     */
    public static function set(string $key, $value, ?string $type = 'string', ?string $group = 'general', ?string $description = null): void
    {
        $val = is_array($value) ? json_encode($value) : $value;
        
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $val, 'type' => $type, 'group' => $group, 'description' => $description]
        );

        Cache::forget("setting.{$key}");
    }
}
