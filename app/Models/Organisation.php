<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organisation extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'organisations';
    protected $guarded = ['id'];


    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_members')->withPivot('role');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'organisation_members')->withPivot('role')->wherePivot('role', '=', 'admin');
    }

    public function editors()
    {
        return $this->belongsToMany(User::class, 'organisation_members')->withPivot('role')->wherePivot('role', '=', 'editor');
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'organisation_members')->withPivot('role')->wherePivot('role', '=', 'viewer');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }


    public function invites()
    {
        return $this->hasMany(Invite::class);
    }


    public function sendInvites($emails)
    {
        foreach ($emails as $email) {
            $this->invites()->create([
                'email' => $email,
                'inviter_id' => auth()->user()->id,
                'token' => Str::random(24),
            ]);
        }
    }
}
