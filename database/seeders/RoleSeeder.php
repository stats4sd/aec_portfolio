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
        $admin = Role::updateOrCreate(['name' => 'Site Admin', 'guard_name' => 'web']);
        $manager = Role::updateOrCreate(['name' => 'Site Manager', 'guard_name' => 'web']);
        $insAdmin = Role::updateOrCreate(['name' => 'Institutional Admin', 'guard_name' => 'web']);
        $insAssessor = Role::updateOrCreate(['name' => 'Institutional Assessor', 'guard_name' => 'web']);
        $insMember = Role::updateOrCreate(['name' => 'Institutional Member', 'guard_name' => 'web']);


        // permissions
        Permission::updateOrCreate(['name' => 'view continents', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view regions', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view countries', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view users', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'maintain users', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'view admin user invites', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'maintain admin user invites', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view red lines', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'maintain red lines', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view principles', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'maintain principles', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view score tags', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'maintain score tags', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'review custom score tags', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view institutions', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'maintain institutions', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view institutional members', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'invite institutional members', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'maintain institutional members', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view institution-level dashboard', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'view portfolio-level dashboard', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view project-level dashboard', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'download institution-level data', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'download portfolio-level data', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'download project-level data', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'download dashboard summary data', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request to remove everything for institution', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view portfolios', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'maintain portfolios', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view projects', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'maintain projects', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'review redlines', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'assess project', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'show institution name and role', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request to remove all personal data from an institution', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'request to leave an institution', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'select institution', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'auto set default institution', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view custom principles', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'maintain custom principles', 'guard_name' => 'web']);

        // extras
        Permission::updateOrCreate(['name' => 'edit own institution', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage user feedback', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage help text entries', 'guard_name' => 'web']);


        // roles_has_permissions
        DB::table('role_has_permissions')->delete();

        $admin->givePermissionTo('view continents');
        $admin->givePermissionTo('view regions');
        $admin->givePermissionTo('view countries');
        $admin->givePermissionTo('view users');
        $admin->givePermissionTo('maintain users');

        $admin->givePermissionTo('view admin user invites');
        $admin->givePermissionTo('maintain admin user invites');
        $admin->givePermissionTo('view red lines');
        $admin->givePermissionTo('maintain red lines');
        $admin->givePermissionTo('view principles');

        $admin->givePermissionTo('maintain principles');
        $admin->givePermissionTo('view score tags');
        $admin->givePermissionTo('maintain score tags');
        $admin->givePermissionTo('review custom score tags');
        $admin->givePermissionTo('view institutions');

        $admin->givePermissionTo('maintain institutions');
        $admin->givePermissionTo('view institutional members');
        $admin->givePermissionTo('invite institutional members');
        $admin->givePermissionTo('maintain institutional members');
        $admin->givePermissionTo('view institution-level dashboard');

        $admin->givePermissionTo('view portfolio-level dashboard');
        $admin->givePermissionTo('view project-level dashboard');
        $admin->givePermissionTo('download institution-level data');
        $admin->givePermissionTo('download portfolio-level data');
        $admin->givePermissionTo('download project-level data');

        $admin->givePermissionTo('download dashboard summary data');
        $admin->givePermissionTo('request to remove everything for institution');
        $admin->givePermissionTo('view portfolios');
        $admin->givePermissionTo('maintain portfolios');
        $admin->givePermissionTo('view projects');

        $admin->givePermissionTo('maintain projects');
        $admin->givePermissionTo('review redlines');
        $admin->givePermissionTo('assess project');

        $admin->givePermissionTo('select institution');
        $admin->givePermissionTo('view custom principles');
        $admin->givePermissionTo('maintain custom principles');

        $admin->givePermissionTo('manage user feedback');
        $admin->givePermissionTo('manage help text entries');


        $manager->givePermissionTo('view red lines');
        $manager->givePermissionTo('maintain red lines');
        $manager->givePermissionTo('view principles');
        $manager->givePermissionTo('maintain principles');
        $manager->givePermissionTo('view score tags');
        $manager->givePermissionTo('maintain score tags');
        $manager->givePermissionTo('review custom score tags');
        $manager->givePermissionTo('view institutions');
        $manager->givePermissionTo('maintain institutions');
        $manager->givePermissionTo('view institutional members');
        $manager->givePermissionTo('select institution');
        $manager->givePermissionTo('view custom principles');
        $manager->givePermissionTo('maintain custom principles');
        $manager->givePermissionTo('view institutional members');
        $manager->givePermissionTo('manage user feedback');
        $manager->givePermissionTo('manage help text entries');


        $insAdmin->givePermissionTo('invite institutional members');
        $insAdmin->givePermissionTo('maintain institutional members');
        $insAdmin->givePermissionTo('view institution-level dashboard');
        $insAdmin->givePermissionTo('view portfolio-level dashboard');
        $insAdmin->givePermissionTo('view project-level dashboard');
        $insAdmin->givePermissionTo('download institution-level data');
        $insAdmin->givePermissionTo('download portfolio-level data');
        $insAdmin->givePermissionTo('download project-level data');
        $insAdmin->givePermissionTo('download dashboard summary data');
        $insAdmin->givePermissionTo('request to remove everything for institution');

        $insAdmin->givePermissionTo('view portfolios');
        $insAdmin->givePermissionTo('maintain portfolios');
        $insAdmin->givePermissionTo('view projects');
        $insAdmin->givePermissionTo('maintain projects');
        $insAdmin->givePermissionTo('review redlines');
        $insAdmin->givePermissionTo('assess project');
        $insAdmin->givePermissionTo('show institution name and role');
        $insAdmin->givePermissionTo('request to remove all personal data from an institution');
        $insAdmin->givePermissionTo('request to leave an institution');
        $insAdmin->givePermissionTo('auto set default institution');

        $insAdmin->givePermissionTo('view custom principles');
        $insAdmin->givePermissionTo('maintain custom principles');
        $insAdmin->givePermissionTo('edit own institution');


        $insAssessor->givePermissionTo('view institution-level dashboard');
        $insAssessor->givePermissionTo('view portfolio-level dashboard');
        $insAssessor->givePermissionTo('view project-level dashboard');
        $insAssessor->givePermissionTo('download dashboard summary data');
        $insAssessor->givePermissionTo('view portfolios');
        $insAssessor->givePermissionTo('maintain portfolios');
        $insAssessor->givePermissionTo('view projects');
        $insAssessor->givePermissionTo('maintain projects');
        $insAssessor->givePermissionTo('review redlines');
        $insAssessor->givePermissionTo('assess project');
        $insAssessor->givePermissionTo('show institution name and role');
        $insAssessor->givePermissionTo('request to remove all personal data from an institution');
        $insAssessor->givePermissionTo('request to leave an institution');
        $insAssessor->givePermissionTo('auto set default institution');
        $insAssessor->givePermissionTo('view custom principles');
        $insAssessor->givePermissionTo('maintain custom principles');


        $insMember->givePermissionTo('view institution-level dashboard');
        $insMember->givePermissionTo('view portfolio-level dashboard');
        $insMember->givePermissionTo('view project-level dashboard');
        $insMember->givePermissionTo('download dashboard summary data');
        $insMember->givePermissionTo('show institution name and role');
        $insMember->givePermissionTo('request to remove all personal data from an institution');
        $insMember->givePermissionTo('request to leave an institution');
        $insMember->givePermissionTo('auto set default institution');
        $insMember->givePermissionTo('view custom principles');
        $insMember->givePermissionTo('maintain custom principles');

    }
}
