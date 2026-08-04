<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Store a shipping address.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:shipping,billing'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ]);

        $user = Auth::user();

        // If it's the first address, make it default automatically
        $isFirst = Address::where('user_id', $user->id)->count() === 0;

        Address::create([
            'user_id' => $user->id,
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address_line1' => $request->input('address_line1'),
            'address_line2' => $request->input('address_line2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
            'is_default' => $isFirst,
        ]);

        return back()->with('success', 'Address added successfully.');
    }

    /**
     * Update an existing address.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ]);

        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->findOrFail($id);

        $address->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address_line1' => $request->input('address_line1'),
            'address_line2' => $request->input('address_line2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
        ]);

        return back()->with('success', 'Address updated successfully.');
    }

    /**
     * Set default address toggle.
     */
    public function makeDefault($id)
    {
        $user = Auth::user();
        
        // Remove default flag from all other addresses
        Address::where('user_id', $user->id)->update(['is_default' => false]);
        
        // Mark current address as default
        Address::where('user_id', $user->id)->findOrFail($id)->update(['is_default' => true]);

        return back()->with('success', 'Default shipping address updated.');
    }

    /**
     * Delete an address.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->findOrFail($id);
        
        $wasDefault = $address->is_default;
        $address->delete();

        // If the deleted address was default, make the next available address default
        if ($wasDefault) {
            $next = Address::where('user_id', $user->id)->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Address deleted successfully.');
    }
}
