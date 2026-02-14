<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@email.com')->first();

        if (!$user) {
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Admin',
                'email' => 'admin@email.com',
                'password' => 'password',
            ]);
        }

        $role = Role::firstOrCreate(['name' => 'Admin']);
        $permissions = Permission::pluck('id')->all();

        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }
}
