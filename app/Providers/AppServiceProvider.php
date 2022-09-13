<?php

namespace App\Providers;

use App\Models\Invite;
use App\Models\Organisation;
use App\Models\RoleInvite;
use App\Models\User;
use App\Observers\InviteObserver;
use App\Observers\RoleInviteObserver;
use App\Observers\OrganisationObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Invite::observe(InviteObserver::class);
        User::observe(UserObserver::class);
        RoleInvite::observe(RoleInviteObserver::class);
    }
}
