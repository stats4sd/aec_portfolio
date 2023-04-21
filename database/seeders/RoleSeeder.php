<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Backpack\PermissionManager\app\Models\Role;
use Backpack\PermissionManager\app\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // roles
        $siteAdmin = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'Site Admin', 'guard_name' => 'web']
        );

        Role::create(['name' => 'Site Manager', 'guard_name' => 'web']);
        Role::create(['name' => 'Institutional Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Institutional Assessor', 'guard_name' => 'web']);
        Role::create(['name' => 'Institutional Member', 'guard_name' => 'web']);


        // permissions
        Permission::create(['name' => 'view continents', 'guard_name' => 'web']);
        Permission::create(['name' => 'view regions', 'guard_name' => 'web']);
        Permission::create(['name' => 'view countries', 'guard_name' => 'web']);
        Permission::create(['name' => 'view users', 'guard_name' => 'web']);
        Permission::create(['name' => 'maintain users', 'guard_name' => 'web']);

        Permission::create(['name' => 'view admin user invites', 'guard_name' => 'web']);
        Permission::create(['name' => 'send admin user invites', 'guard_name' => 'web']);
        Permission::create(['name' => 'view red lines', 'guard_name' => 'web']);
        Permission::create(['name' => 'maintain red lines', 'guard_name' => 'web']);
        Permission::create(['name' => 'view principles', 'guard_name' => 'web']);

        Permission::create(['name' => 'maintain principles', 'guard_name' => 'web']);
        Permission::create(['name' => 'view score tags', 'guard_name' => 'web']);
        Permission::create(['name' => 'maintain score tags', 'guard_name' => 'web']);
        Permission::create(['name' => 'review custom score tags', 'guard_name' => 'web']);
        Permission::create(['name' => 'view institutions', 'guard_name' => 'web']);

        Permission::create(['name' => 'maintain institutions', 'guard_name' => 'web']);
        Permission::create(['name' => 'invite institutional members', 'guard_name' => 'web']);
        Permission::create(['name' => 'update role of institutional members', 'guard_name' => 'web']);
        Permission::create(['name' => 'maintain institutional members', 'guard_name' => 'web']);
        Permission::create(['name' => 'view institution-level dashboard', 'guard_name' => 'web']);

        Permission::create(['name' => 'view portfolio-level dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'view project-level dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'download institution-level data', 'guard_name' => 'web']);
        Permission::create(['name' => 'download portfolio-level data', 'guard_name' => 'web']);
        Permission::create(['name' => 'download project-level data', 'guard_name' => 'web']);

        Permission::create(['name' => 'download dashboard summary data', 'guard_name' => 'web']);
        // Permission::create(['name' => 'create new custom score tags', 'guard_name' => 'web']);
        Permission::create(['name' => 'request to remove everything for institution', 'guard_name' => 'web']);
        Permission::create(['name' => 'view portfolios', 'guard_name' => 'web']);
        Permission::create(['name' => 'maintain portfolios', 'guard_name' => 'web']);
        Permission::create(['name' => 'view projects', 'guard_name' => 'web']);

        Permission::create(['name' => 'maintain projects', 'guard_name' => 'web']);
        Permission::create(['name' => 'review redlines', 'guard_name' => 'web']);
        Permission::create(['name' => 'assess project', 'guard_name' => 'web']);
        Permission::create(['name' => 'show institution name and role', 'guard_name' => 'web']);
        Permission::create(['name' => 'request to remove all personal data from an institution', 'guard_name' => 'web']);

        Permission::create(['name' => 'request to leave an institution', 'guard_name' => 'web']);
        Permission::create(['name' => 'select institution', 'guard_name' => 'web']);
        Permission::create(['name' => 'auto set default institution', 'guard_name' => 'web']);
        Permission::create(['name' => 'view custom principles', 'guard_name' => 'web']);
        Permission::create(['name' => 'maintain custom principles', 'guard_name' => 'web']);



        // roles_has_permissions
        DB::table('role_has_permissions')->delete();

        // site admin
        DB::table('role_has_permissions')->insert([
            [ 'role_id' => 1, 'permission_id' => 1, ],
            [ 'role_id' => 1, 'permission_id' => 2, ],
            [ 'role_id' => 1, 'permission_id' => 3, ],
            [ 'role_id' => 1, 'permission_id' => 4, ],
            [ 'role_id' => 1, 'permission_id' => 5, ],
            [ 'role_id' => 1, 'permission_id' => 6, ],
            [ 'role_id' => 1, 'permission_id' => 7, ],
            [ 'role_id' => 1, 'permission_id' => 8, ],
            [ 'role_id' => 1, 'permission_id' => 9, ],
            [ 'role_id' => 1, 'permission_id' => 10, ],
            [ 'role_id' => 1, 'permission_id' => 11, ],
            [ 'role_id' => 1, 'permission_id' => 12, ],
            [ 'role_id' => 1, 'permission_id' => 13, ],
            [ 'role_id' => 1, 'permission_id' => 14, ],
            [ 'role_id' => 1, 'permission_id' => 15, ],
            [ 'role_id' => 1, 'permission_id' => 16, ],
            [ 'role_id' => 1, 'permission_id' => 17, ],
            [ 'role_id' => 1, 'permission_id' => 18, ],
            [ 'role_id' => 1, 'permission_id' => 19, ],
            [ 'role_id' => 1, 'permission_id' => 20, ],
            [ 'role_id' => 1, 'permission_id' => 21, ],
            [ 'role_id' => 1, 'permission_id' => 22, ],
            [ 'role_id' => 1, 'permission_id' => 23, ],
            [ 'role_id' => 1, 'permission_id' => 24, ],
            [ 'role_id' => 1, 'permission_id' => 25, ],
            [ 'role_id' => 1, 'permission_id' => 26, ],
            // [ 'role_id' => 1, 'permission_id' => 27, ],
            [ 'role_id' => 1, 'permission_id' => 27, ],
            [ 'role_id' => 1, 'permission_id' => 28, ],
            [ 'role_id' => 1, 'permission_id' => 29, ],
            [ 'role_id' => 1, 'permission_id' => 30, ],
            [ 'role_id' => 1, 'permission_id' => 31, ],
            [ 'role_id' => 1, 'permission_id' => 32, ],
            [ 'role_id' => 1, 'permission_id' => 33, ],
            [ 'role_id' => 1, 'permission_id' => 37, ],
            [ 'role_id' => 1, 'permission_id' => 39, ],
            [ 'role_id' => 1, 'permission_id' => 40, ],
        ]);
        
        // site manager
        DB::table('role_has_permissions')->insert([
            [ 'role_id' => 2, 'permission_id' => 8, ],
            [ 'role_id' => 2, 'permission_id' => 9, ],
            [ 'role_id' => 2, 'permission_id' => 10, ],
            [ 'role_id' => 2, 'permission_id' => 11, ],
            [ 'role_id' => 2, 'permission_id' => 12, ],
            [ 'role_id' => 2, 'permission_id' => 13, ],
            [ 'role_id' => 2, 'permission_id' => 14, ],
            [ 'role_id' => 2, 'permission_id' => 15, ],
            [ 'role_id' => 2, 'permission_id' => 16, ],
            [ 'role_id' => 2, 'permission_id' => 17, ],
            [ 'role_id' => 2, 'permission_id' => 37, ],
            [ 'role_id' => 2, 'permission_id' => 39, ],
            [ 'role_id' => 2, 'permission_id' => 40, ],
        ]);

        // institutional admin
        DB::table('role_has_permissions')->insert([
            [ 'role_id' => 3, 'permission_id' => 17, ],
            [ 'role_id' => 3, 'permission_id' => 18, ],
            [ 'role_id' => 3, 'permission_id' => 19, ],
            [ 'role_id' => 3, 'permission_id' => 20, ],
            [ 'role_id' => 3, 'permission_id' => 21, ],
            [ 'role_id' => 3, 'permission_id' => 22, ],
            [ 'role_id' => 3, 'permission_id' => 23, ],
            [ 'role_id' => 3, 'permission_id' => 24, ],
            [ 'role_id' => 3, 'permission_id' => 25, ],
            [ 'role_id' => 3, 'permission_id' => 26, ],
            // [ 'role_id' => 3, 'permission_id' => 27, ],
            [ 'role_id' => 3, 'permission_id' => 27, ],
            [ 'role_id' => 3, 'permission_id' => 28, ],
            [ 'role_id' => 3, 'permission_id' => 29, ],
            [ 'role_id' => 3, 'permission_id' => 30, ],
            [ 'role_id' => 3, 'permission_id' => 31, ],
            [ 'role_id' => 3, 'permission_id' => 32, ],
            [ 'role_id' => 3, 'permission_id' => 33, ],
            [ 'role_id' => 3, 'permission_id' => 34, ],
            [ 'role_id' => 3, 'permission_id' => 35, ],
            [ 'role_id' => 3, 'permission_id' => 36, ],
            [ 'role_id' => 3, 'permission_id' => 38, ],
            [ 'role_id' => 3, 'permission_id' => 39, ],
            [ 'role_id' => 3, 'permission_id' => 40, ],
        ]);

        // institutional assessor
        DB::table('role_has_permissions')->insert([
            [ 'role_id' => 4, 'permission_id' => 20, ],
            [ 'role_id' => 4, 'permission_id' => 21, ],
            [ 'role_id' => 4, 'permission_id' => 22, ],
            [ 'role_id' => 4, 'permission_id' => 26, ],
            // [ 'role_id' => 4, 'permission_id' => 27, ],
            [ 'role_id' => 4, 'permission_id' => 27, ],
            [ 'role_id' => 4, 'permission_id' => 28, ],
            [ 'role_id' => 4, 'permission_id' => 29, ],
            [ 'role_id' => 4, 'permission_id' => 30, ],
            [ 'role_id' => 4, 'permission_id' => 31, ],
            [ 'role_id' => 4, 'permission_id' => 32, ],
            [ 'role_id' => 4, 'permission_id' => 33, ],
            [ 'role_id' => 4, 'permission_id' => 34, ],
            [ 'role_id' => 4, 'permission_id' => 35, ],
            [ 'role_id' => 4, 'permission_id' => 36, ],
            [ 'role_id' => 4, 'permission_id' => 38, ],
            [ 'role_id' => 4, 'permission_id' => 39, ],
            [ 'role_id' => 4, 'permission_id' => 40, ],
        ]);
        
        // institutional member
        DB::table('role_has_permissions')->insert([
            [ 'role_id' => 5, 'permission_id' => 20, ],
            [ 'role_id' => 5, 'permission_id' => 21, ],
            [ 'role_id' => 5, 'permission_id' => 22, ],
            [ 'role_id' => 5, 'permission_id' => 26, ],
            [ 'role_id' => 5, 'permission_id' => 34, ],
            [ 'role_id' => 5, 'permission_id' => 35, ],
            [ 'role_id' => 5, 'permission_id' => 36, ],
            [ 'role_id' => 5, 'permission_id' => 38, ],
            [ 'role_id' => 5, 'permission_id' => 39, ],
            [ 'role_id' => 5, 'permission_id' => 40, ],
        ]);

    }
}
