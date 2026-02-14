<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define menu modules
        $menus = [
            'Users',
            'Roles',
            'Tickets',
        ];

        // Create permissions for each menu
        foreach ($menus as $menu) {
            $lowerMenu = strtolower($menu);
            Permission::firstOrCreate(['name' => "{$lowerMenu}-read"]);
            Permission::firstOrCreate(['name' => "{$lowerMenu}-create"]);
            Permission::firstOrCreate(['name' => "{$lowerMenu}-update"]);
            Permission::firstOrCreate(['name' => "{$lowerMenu}-delete"]);
        }

        // Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        // Create Admin User
        $adminUser = User::where('email', 'admin@email.com')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Admin',
                'email' => 'admin@email.com',
                'password' => 'password',
                'is_active' => 1,
            ]);
        }
        $adminUser->assignRole($adminRole);

        // Create Support Role
        $supportRole = Role::firstOrCreate(['name' => 'Support']);
        $supportPermissions = Permission::whereIn('name', [
            'tickets-read',
            'tickets-update',
        ])->pluck('name')->toArray();
        $supportRole->syncPermissions($supportPermissions);

        // Create Support User
        $supportUser = User::where('email', 'support@email.com')->first();
        if (!$supportUser) {
            $supportUser = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Support Staff',
                'email' => 'support@email.com',
                'password' => 'password',
                'is_active' => 1,
            ]);
        }
        $supportUser->assignRole($supportRole);

        // Create User Role
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userPermissions = Permission::whereIn('name', [
            'tickets-read',
            'tickets-create',
            'tickets-update',
        ])->pluck('name')->toArray();
        $userRole->syncPermissions($userPermissions);

        // Create Regular User
        $regularUser = User::where('email', 'user@email.com')->first();
        if (!$regularUser) {
            $regularUser = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Regular User',
                'email' => 'user@email.com',
                'password' => 'password',
                'is_active' => 1,
            ]);
        }
        $regularUser->assignRole($userRole);
    }
}
