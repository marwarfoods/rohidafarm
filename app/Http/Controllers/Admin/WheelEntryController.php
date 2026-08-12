<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WheelEntry;
use Illuminate\Http\Request;

class WheelEntryController extends Controller
{
    public function index()
    {
        $entries = WheelEntry::latest()->paginate(20);
        return view('admin.wheel-entries.index', compact('entries'));
    }

    public function destroy($id)
    {
        $entry = WheelEntry::findOrFail($id);
        $entry->delete();
        return redirect()->route('admin.wheel-entries.index')->with('success', 'Entry deleted successfully.');
    }
}
