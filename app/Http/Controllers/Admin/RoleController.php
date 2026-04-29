<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage roles & permissions');
        $this->middleware('permission:edit roles')->only('edit', 'update');
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();

        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $prefix = $isAdmin ? 'admin.' : '';
        $role = Role::create([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route($prefix . 'roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $prefix = $isAdmin ? 'admin.' : '';

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route($prefix . 'roles.index');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return back();
    }
}
