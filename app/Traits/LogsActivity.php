<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log user activity.
     */
    public static function logActivity(string $action, ?string $description = null, ?array $payload = null): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'payload' => $payload,
            ]);
        } catch (\Exception $e) {
            // Silently catch logging failures in CLI or background queues if DB is not ready
            logger()->error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
