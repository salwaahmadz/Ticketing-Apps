<?php

namespace App\Repositories\Implementations;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RoleRepository implements RoleRepositoryInterface
{
    protected const ROLE_NOT_FOUND = 'Role not found';

    public function getAll()
    {
        $roles = Role::query()
            ->with('permissions')
            ->orderBy('id', 'desc')
            ->paginate(10);

        foreach ($roles as $role) {
            $role->users_count = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', 'App\Models\User')
                ->count();
        }

        return $roles;
    }

    public function findByUuid($uuid)
    {
        $result = [
            'error' => false,
            'message' => '',
            'data' => null,
        ];

        $data = Role::with('permissions')->where('uuid', $uuid)->first();

        if (!$data) {
            $result['error'] = true;
            $result['message'] = self::ROLE_NOT_FOUND;
            return $result;
        }

        $result['data'] = $data;
        return $result;
    }

    public function store($data)
    {
        $result = [
            'error' => true,
            'message' => 'Failed to add Role',
            'data' => null,
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:roles,name',
            'permission' => 'nullable|array',
            'permission.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Name is required',
            'name.unique' => 'Role name already exists',
            'permission.array' => 'Permissions must be an array',
        ]);

        if ($validator->fails()) {
            $result['message'] = implode(', ', $validator->errors()->all());
            return $result;
        }

        $roleName = trim($data['name']);
        if (strtolower($roleName) === 'admin') {
            $existingAdmin = Role::whereRaw('LOWER(name) = ?', ['admin'])->first();
            if ($existingAdmin) {
                $result['message'] = 'Role "Admin" already exists. Only one Admin role is allowed.';
                return $result;
            }
        }

        try {
            DB::beginTransaction();

            $role = Role::create([
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            // Sync permissions directly by name
            if (!empty($data['permission'])) {
                $role->syncPermissions($data['permission']);
            }

            DB::commit();

            $result['error'] = false;
            $result['message'] = 'Success to add Role';
            $result['data'] = $role->load('permissions');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Role store failed', ['error' => $th->getMessage()]);
            $result['message'] = 'System error: ' . $th->getMessage();
        }

        return $result;
    }

    public function update($uuid, $data)
    {
        $result = [
            'error' => true,
            'message' => 'Failed to update Role',
            'data' => null,
        ];

        $role = Role::where('uuid', $uuid)->first();

        if (!$role) {
            $result['message'] = self::ROLE_NOT_FOUND;
            return $result;
        }

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permission' => 'nullable|array',
            'permission.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Name is required',
            'name.unique' => 'Role name already exists',
            'permission.array' => 'Permissions must be an array',
        ]);

        if ($validator->fails()) {
            $result['message'] = implode(', ', $validator->errors()->all());
            return $result;
        }

        $currentRoleName = trim($role->name);
        $newRoleName = trim($data['name']);

        if (strtolower($currentRoleName) === 'admin' && strtolower($newRoleName) !== 'admin') {
            $result['message'] = 'Role "Admin" name cannot be changed.';
            return $result;
        }

        if (strtolower($newRoleName) === 'admin') {
            $existingAdmin = Role::whereRaw('LOWER(name) = ?', ['admin'])
                ->where('uuid', '!=', $uuid)
                ->first();
            if ($existingAdmin) {
                $result['message'] = 'Role "Admin" already exists. Only one Admin role is allowed.';
                return $result;
            }
        }

        try {
            DB::beginTransaction();

            $role->update([
                'name' => $data['name'],
            ]);

            // Sync permissions directly by name
            $role->syncPermissions($data['permission'] ?? []);

            DB::commit();

            $result['error'] = false;
            $result['message'] = 'Success to update Role';
            $result['data'] = $role->load('permissions');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Role update failed', ['error' => $th->getMessage()]);
            $result['message'] = 'System error: ' . $th->getMessage();
        }

        return $result;
    }

    public function destroy($uuid)
    {
        $result = [
            'error' => false,
            'message' => '',
            'data' => null,
        ];

        $data = Role::where('uuid', $uuid)->first();

        if (!$data) {
            $result['error'] = true;
            $result['message'] = self::ROLE_NOT_FOUND;
            return $result;
        }

        try {
            DB::beginTransaction();
            $data->delete();
            DB::commit();

            $result['message'] = 'Success to delete Role';
        } catch (\Throwable $th) {
            DB::rollBack();
            $result['error'] = true;
            $result['message'] = $th->getMessage();
        }

        return $result;
    }

    private function mapMenuPermissionsToIds(array $menuPermissions): array
    {
        if (empty($menuPermissions)) {
            return [];
        }

        $permissionIds = [];

        foreach ($menuPermissions as $menu => $types) {
            if (!is_array($types)) {
                continue;
            }

            if (in_array('all', $types)) {
                $menuPermissionIds = Permission::where('grouping', $menu)->pluck('id')->toArray();
                $permissionIds = array_merge($permissionIds, $menuPermissionIds);
                continue;
            }

            $typeMapping = [
                'read' => ['read'],
                'create' => ['create'],
                'update' => ['update'],
                'delete' => ['delete'],
            ];

            foreach ($types as $type) {
                if (!isset($typeMapping[$type])) {
                    continue;
                }

                $searchTerms = $typeMapping[$type];

                $matchedPermissions = Permission::where('grouping', $menu)
                    ->where(function ($query) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $query->orWhere('name', 'like', '%' . strtolower($term) . '%')
                                ->orWhere('show_name', 'like', '%' . ucfirst($term) . '%');
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                $permissionIds = array_merge($permissionIds, $matchedPermissions);
            }
        }

        return array_unique($permissionIds);
    }

    public function getPermission()
    {
        $permissions = [];

        // Get all permissions
        $allPermissions = Permission::orderBy('name', 'asc')->get();

        // Group permissions by module (e.g., 'users', 'roles', 'tickets')
        $groupedPermissions = [];

        foreach ($allPermissions as $permission) {
            // Extract group from permission name (e.g., 'users-read' -> 'users')
            $parts = explode('-', $permission->name);
            $module = $parts[0] ?? 'other';

            if (!isset($groupedPermissions[$module])) {
                $groupedPermissions[$module] = [];
            }

            $groupedPermissions[$module][] = $permission;
        }

        // Sort modules and ensure order: users, roles, tickets, others
        $sortOrder = ['users' => 1, 'roles' => 2, 'tickets' => 3];
        uksort($groupedPermissions, function ($a, $b) use ($sortOrder) {
            $orderA = $sortOrder[$a] ?? 999;
            $orderB = $sortOrder[$b] ?? 999;
            if ($orderA === $orderB) {
                return strcmp($a, $b);
            }
            return $orderA - $orderB;
        });

        // Format for view
        foreach ($groupedPermissions as $module => $perms) {
            // Sort permissions within each module: read, create, update, delete
            $order = ['read' => 1, 'create' => 2, 'update' => 3, 'delete' => 4];
            usort($perms, function ($a, $b) use ($order) {
                $typeA = explode('-', $a->name)[1] ?? 'zzz';
                $typeB = explode('-', $b->name)[1] ?? 'zzz';
                $orderA = $order[$typeA] ?? 999;
                $orderB = $order[$typeB] ?? 999;
                return $orderA - $orderB;
            });

            $permissions[] = [
                'group' => ucfirst($module),
                'list' => collect($perms),
            ];
        }

        return $permissions;
    }
}
