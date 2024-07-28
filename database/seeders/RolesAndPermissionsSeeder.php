<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use function PHPSTORM_META\map;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'show yard']);
        Permission::create(['name' => 'create yard']);
        Permission::create(['name' => 'edit yard']);
        Permission::create(['name' => 'delete yard']);

        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'show user']);


        Permission::create(['name' => 'create tractor']);
        Permission::create(['name' => 'edit tractor']);
        Permission::create(['name' => 'delete tractor']);
        Permission::create(['name' => 'show tractor']);


        Permission::create(['name' => 'create yard admin user']);
        Permission::create(['name' => 'edit yard admin user']);
        Permission::create(['name' => 'delete yard admin user']);
        Permission::create(['name' => 'show yard admin user']);

        Permission::create(['name' => 'user']);


        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'Super Admin']);
        $adminRole->givePermissionTo([
            'show yard',
            'create yard',
            'edit yard',
            'delete yard',
            'create yard admin user',
            'edit yard admin user',
            'delete yard admin user',
            'show yard admin user',
        ]);

        $yardAdmin = Role::create(['name' => 'Yard Admin']);
        $yardAdmin->givePermissionTo([
            'create tractor',
            'edit tractor',
            'delete tractor',
            'show tractor',
            'create user',
            'edit user',
            'delete user',
            'show user',
        ]);

        $normalUser = Role::create(['name' => 'User']);
        $normalUser->givePermissionTo([
            'user'
        ]);

    }
}
