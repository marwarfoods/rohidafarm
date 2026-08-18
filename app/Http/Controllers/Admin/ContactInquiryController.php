<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of contact form inquiries.
     */
    public function index(Request $request)
    {
        $query = ContactInquiry::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->latest()->paginate(15)->withQueryString();

        return view('admin.contact-inquiries.index', compact('inquiries'));
    }

    /**
     * Mark an inquiry as read and return details.
     */
    public function markAsRead($id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Marked as read.',
                'inquiry' => $inquiry,
            ]);
        }

        return back()->with('success', 'Inquiry marked as read.');
    }

    /**
     * Delete an inquiry.
     */
    public function destroy($id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $name = $inquiry->name;
        $inquiry->delete();

        self::logActivity('contact_inquiry_delete', "Deleted contact form entry from {$name}");

        return redirect()->route('admin.contact-inquiries.index')->with('success', 'Contact form entry deleted successfully.');
    }
}
