<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display all customers with order count.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->with(['orders', 'roles']);

        // Search by name, email or phone
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        // Dynamic pagination
        $perPage = $request->input('per_page', 20);
        
        // Ensure perPage is a valid number (or very large for "All")
        if ($perPage === 'all') {
            $perPage = $query->count();
            // Prevent error if count is 0
            $perPage = $perPage > 0 ? $perPage : 1;
        } else {
            $perPage = (int)$perPage > 0 ? (int)$perPage : 20;
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Helper to get customer export query
     */
    private function getExportQuery(Request $request)
    {
        $query = User::where('role', 'customer')->withCount('orders');

        // Search logic from index
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        // If specific IDs are selected
        if ($request->filled('selected_ids')) {
            $ids = explode(',', $request->input('selected_ids'));
            $query->whereIn('id', $ids);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Export customers as CSV.
     */
    public function exportCsv(Request $request)
    {
        $customers = $this->getExportQuery($request)->get();

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=customers_export_' . date('Y_m_d_His') . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public',
        ];

        $columns = ['ID', 'Name', 'Email', 'Phone', 'Total Orders', 'Joined At'];

        $callback = function () use ($customers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($customers as $customer) {
                $row = [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->orders_count,
                    $customer->created_at->format('Y-m-d H:i:s'),
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export customers as PDF.
     */
    public function exportPdf(Request $request)
    {
        $customers = $this->getExportQuery($request)->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.customers.pdf', compact('customers'));
        
        return $pdf->download('customers_export_' . date('Y_m_d_His') . '.pdf');
    }

    /**
     * Show a single customer with all their orders.
     */
    public function show($id)
    {
        $customer = User::where('role', 'customer')
            ->with(['orders.items.product', 'addresses'])
            ->findOrFail($id);

        $orders = $customer->orders()->with('items.product')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    /**
     * Show create customer form.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a new customer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Show edit form for a customer.
     */
    public function edit($id)
    {
        $customer = User::findOrFail($id);
        $roles = \App\Models\Role::all();
        return view('admin.customers.edit', compact('customer', 'roles'));
    }

    /**
     * Update customer details.
     */
    public function update(Request $request, $id)
    {
        $customer = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($customer->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|string' // can be "customer" or role ID
        ]);

        $data = [
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        // Sync role assignment
        if ($request->filled('role_id')) {
            if ($request->role_id === 'customer') {
                $customer->roles()->detach();
                $customer->update(['role' => 'customer']);
            } else {
                $role = \App\Models\Role::find($request->role_id);
                if ($role) {
                    $customer->roles()->sync([$role->id]);
                    // If assigned admin role, update the role column as well
                    if ($role->name === 'admin') {
                        $customer->update(['role' => 'admin']);
                    } else {
                        $customer->update(['role' => 'customer']);
                    }
                }
            }
        }

        return redirect()->route('admin.customers.show', $customer->id)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Permanently delete a customer.
     */
    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $name = $customer->name;
        $customer->roles()->detach();
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer \"{$name}\" has been deleted.");
    }
}
