<?php

namespace App\Repositories\Implementations;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAll()
    {
        return User::query();
    }

    /**
     * Get user by uuid
     *
     * @param string $uuid
     * @return User
     */
    public function getByUuid(string $uuid)
    {
        return User::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        try {
            $data += [
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            $user = User::create($data);

            if (isset($data['role']) && $data['role']) {
                $role = Role::find($data['role']);
                if ($role) {
                    $user->assignRole($role);
                }
            }

            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(string $uuid, array $data)
    {
        try {
            $user = $this->getByUuid($uuid);
            $user->update($data);

            if (isset($data['role']) && $data['role']) {
                $role = Role::find($data['role']);
                if ($role) {
                    $user->syncRoles($role);
                }
            }

            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(string $uuid)
    {
        try {
            $user = $this->getByUuid($uuid);
            $user->delete();
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
