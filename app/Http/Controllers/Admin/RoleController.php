<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Display Roles & Permissions index.
     */
    public function index(Request $request)
    {
        $roles = Role::whereNotIn('name', ['admin', 'customer'])->with('permissions')->get();
        $permissions = Permission::all();

        // Get users who have roles (administrative staff)
        $staffQuery = User::whereHas('roles')->with('roles');
        
        if ($request->has('search_staff') && $request->input('search_staff')) {
            $search = $request->input('search_staff');
            $staffQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $staff = $staffQuery->paginate(10, ['*'], 'staff_page');

        // Search for users to assign new roles
        $users = [];
        if ($request->has('search_user') && $request->input('search_user')) {
            $searchUser = $request->input('search_user');
            $users = User::where('name', 'like', "%{$searchUser}%")
                         ->orWhere('email', 'like', "%{$searchUser}%")
                         ->limit(10)
                         ->get();
        }

        return view('admin.roles.index', compact('roles', 'permissions', 'staff', 'users'));
    }

    /**
     * Store new role.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:50',
            'display_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:250',
            'permissions' => 'nullable|array',
        ]);

        if (in_array(strtolower($request->input('name')), ['admin', 'customer'])) {
            return back()->with('error', 'Cannot create a role with system reserved name.');
        }

        try {
            $role = Role::create([
                'name' => strtolower(str_replace(' ', '-', $request->input('name'))),
                'display_name' => $request->input('display_name') ?? $request->input('name'),
                'description' => $request->input('description'),
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions'));
            }

            return back()->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update role.
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['admin', 'customer'])) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $id,
            'display_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:250',
            'permissions' => 'nullable|array',
        ]);

        try {
            $role->update([
                'name' => strtolower(str_replace(' ', '-', $request->input('name'))),
                'display_name' => $request->input('display_name') ?? $request->input('name'),
                'description' => $request->input('description'),
            ]);

            $role->permissions()->sync($request->input('permissions') ?? []);

            return back()->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete role.
     */
    public function destroyRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            if (in_array($role->name, ['admin', 'customer'])) {
                return back()->with('error', 'System roles cannot be deleted.');
            }
            $role->users()->detach();
            $role->permissions()->detach();
            $role->delete();

            return back()->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array',
        ]);

        try {
            $user = User::findOrFail($request->input('user_id'));
            $user->roles()->sync($request->input('roles'));

            return back()->with('success', 'Roles assigned to user successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove role from user.
     */
    public function removeUserRole($userId, $roleId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->roles()->detach($roleId);

            return back()->with('success', 'Role removed from user.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
