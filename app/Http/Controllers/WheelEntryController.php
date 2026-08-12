<?php

namespace App\Http\Controllers;

use App\Models\WheelEntry;
use Illuminate\Http\Request;

class WheelEntryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
        ]);

        // Save only the first time a mobile number is seen; a duplicate
        // is treated as success so the visitor still gets to spin.
        WheelEntry::firstOrCreate(
            ['mobile_number' => $validated['mobile_number']],
            ['name' => $validated['name']]
        );

        return response()->json(['success' => true]);
    }
}
