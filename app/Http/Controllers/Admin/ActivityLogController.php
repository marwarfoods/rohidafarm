<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display log trails.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30);

        return view('admin.logs.index', compact('logs'));
    }

    /**
     * Clear all logs.
     */
    public function destroyAll()
    {
        ActivityLog::truncate();
        return back()->with('success', 'Activity logs cleared successfully.');
    }
}
