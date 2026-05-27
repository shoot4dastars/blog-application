<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();

        // Get all permissions
        $permissions = Permission::all();

        // If no permissions exist, create default ones
        if ($permissions->isEmpty()) {
            $defaultPermissions = [
                ['name' => 'View Posts', 'route_name' => 'posts.index'],
                ['name' => 'Create Posts', 'route_name' => 'posts.create'],
                ['name' => 'Edit Posts', 'route_name' => 'posts.edit'],
                ['name' => 'Delete Posts', 'route_name' => 'posts.destroy'],
                ['name' => 'View Dashboard', 'route_name' => 'dashboard'],
                ['name' => 'Admin Access', 'route_name' => 'admin-access'],
            ];

            foreach ($defaultPermissions as $perm) {
                Permission::create($perm);
            }

            $permissions = Permission::all();
        }

        return view('admin.roles', compact('roles', 'permissions'));
    }

    public function assignPermission(Request $request, Role $role)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        // Prevent modifying admin role permissions
        $roleName = $role->name->value ?? $role->name;
        if ($roleName === 'admin') {
            return redirect()->back()->with('error', 'Admin role permissions cannot be modified. Admins have all permissions by default.');
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Sync permissions (replace all existing with new ones)
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->back()->with('success', 'Permissions updated successfully for ' . ucfirst($roleName));
    }
}
