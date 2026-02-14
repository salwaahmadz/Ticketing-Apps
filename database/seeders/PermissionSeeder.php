<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            'Users',
            'Roles',
        ];

        $adminRole = Role::where(['name' => 'Admin'])->first();

        foreach ($menus as $menu) {
            $checkMenu = Permission::where('grouping', $menu)->first();

            if (!$checkMenu) {
                $currentDate = date('Y-m-d H:i:s');

                $permissions = [
                    [
                        'name' => Str::slug($menu . ' Read'),
                        'guard_name' => 'web',
                        'show_name' => $menu . ' Read',
                        'sorting' => 1,
                        'grouping' => $menu,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ],
                    [
                        'name' => Str::slug($menu . ' Create'),
                        'guard_name' => 'web',
                        'show_name' => $menu . ' Create',
                        'sorting' => 2,
                        'grouping' => $menu,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ],
                    [
                        'name' => Str::slug($menu . ' Update'),
                        'guard_name' => 'web',
                        'show_name' => $menu . ' Update',
                        'sorting' => 3,
                        'grouping' => $menu,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ],
                    [
                        'name' => Str::slug($menu . ' Delete'),
                        'guard_name' => 'web',
                        'show_name' => $menu . ' Delete',
                        'sorting' => 4,
                        'grouping' => $menu,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ],
                ];

                foreach ($permissions as $permission) {
                    Permission::firstOrCreate(
                        [
                            'name' => $permission['name'],
                            'guard_name' => $permission['guard_name']
                        ],
                        $permission
                    );
                }
            }
        }

        $allPermissions = Permission::pluck('id', 'id')->all();

        if ($adminRole) {
            $adminRole->syncPermissions($allPermissions);
        }
    }
}
