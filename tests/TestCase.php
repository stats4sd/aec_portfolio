<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setupSiteAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole(['Site Admin']);

        return $user;
    }

    public function setupSiteManagerUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole(['Site Manager']);

        return $user;
    }

    public function setupInstitutionalAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole(['Institutional Admin']);

        return $user;
    }

    public function setupInstitutionalAssessorUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole(['Institutional Assessor']);

        return $user;
    }
    
    public function setupInstitutionalMemberUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole(['Institutional Member']);

        return $user;
    }

}
