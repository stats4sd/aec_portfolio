<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateTeamLevelRoleRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:regenerate-team-level-roles-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate team level roles records according to users, organisation_members and model_has_roles tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('start');

        $allOrganisations = Organisation::all();

        // $users = User::where('id', '=', 9)->with('roles')->get();
        $users = User::with('roles')->get();

        foreach ($users as $user) {
            $this->comment($user->id . ' - ' . $user->name);

            // try to find user's role by using API but failed
            // $this->comment(' ===> ' . $user->roles);
            // $this->comment(' ===> ' . $user->getRoleNames());

            // Get the mininum role_id of user, suppose user can have one roles for any organisation currently
            $roles = DB::select('select min(role_id) as min_role_id from model_has_roles where model_id = ' . $user->id);
            $minRoleId = $roles[0]->min_role_id;
            $this->comment(' min_role_id ===> ' . $minRoleId);

            // Remove existing model_has_roles belong to user
            $deleteSql = "DELETE FROM model_has_roles WHERE model_id = " . $user->id;
            DB::statement($deleteSql);

            // Add role per user per organisations
            if ($minRoleId == 1 || $minRoleId == 2) {
                // handling for Site Admin, Site Manager
                foreach ($allOrganisations as $organisation) {
                    $this->comment(' - ' . $organisation->name);
                    $insertSql = "INSERT INTO model_has_roles values ($minRoleId, 'App\\\Models\\\User', " . $user->id . ", " . $organisation->id . ")";
                    DB::statement($insertSql);
                }
            } else {
                // handling for Ins Admin, Ins Assessor, Ins Member
                foreach ($user->organisations as $organisation) {
                    $this->comment(' - ' . $organisation->name);
                    $insertSql = "INSERT INTO model_has_roles values ($minRoleId, 'App\\\Models\\\User', " . $user->id . ", " . $organisation->id . ")";
                    DB::statement($insertSql);
                }
            }
        }

        $this->info('end');
    }
}
