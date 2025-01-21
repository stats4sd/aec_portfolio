<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, CrudTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function organisations()
    {
        return $this->belongsToMany(Organisation::class, 'organisation_members')
            ->withPivot('role');
    }

    public function removalRequests()
    {
        return $this->hasMany(RemovalRequest::class, 'requester_id');
    }

    public function userFeedBacks(): HasMany
    {
        return $this->hasMany(UserFeedback::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    public function isAdmin()
    {
        // old approach: check if user has Site Admin role
        return $this->hasAnyRole('Site Admin');

        // new approach: check if user has Site Admin role for any organisation
        // $result = DB::select('SELECT COUNT(*) as record_count FROM model_has_roles WHERE role_id = 1 AND model_id = ' . auth()->user()->id);
        // $recordCount = $result[0]->record_count;

        // return $recordCount > 0;
    }

    public function withPermissionNames()
    {
        $this->permission_names = $this->roles->map(fn(Role $role): ?Collection => $role->permissions)
            ->flatten()
            ->map(fn(Permission $permission): string => $permission->name);
        return $this;
    }

    /** Has the user signed data sharing agreements for any organisations? */
    public function signed_organisations(): HasMany
    {
        return $this->hasMany(Organisation::class, 'signee_id');
    }
}
