<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleController extends Controller
{
    private $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        $roles = $this->roleRepository->getAll();
        return view('cms.pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->roleRepository->getPermission();
        $rolePermissionNames = []; // Empty for create mode
        return view('cms.pages.roles.create', compact('permissions', 'rolePermissionNames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permission' => 'array',
        ]);

        $this->roleRepository->store($validated);

        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    public function edit($uuid)
    {
        $result = $this->roleRepository->findByUuid($uuid);

        if ($result['error']) {
            return redirect()->route('roles.index')->with('error', $result['message']);
        }

        $role = $result['data'];
        $permissions = $this->roleRepository->getPermission();
        $rolePermissionNames = $role->permissions->pluck('name')->toArray();

        return view('cms.pages.roles.update', compact('role', 'permissions', 'rolePermissionNames'));
    }

    public function update(Request $request, $uuid)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permission' => 'array',
        ]);

        $this->roleRepository->update($uuid, $validated);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy($uuid)
    {
        $this->roleRepository->destroy($uuid);

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
