<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OrganisationMember extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'organisation_members';
    protected $guarded = ['id'];


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // public function project() {
    //     // consultant_projects.project_id links to projects.id
    //     return $this->belongsTo(Project::class);
    // }

    // public function member() {
    //     // consultant_projects.member_id links to members.id
    //     return $this->belongsTo(Member::class);
    // }


    public function user() {
        // organisation_member.user_id links to users.id
        return $this->belongsTo(User::class);
    }

    public function organisation() {
        // organisation_member.organisation_id links to organisations.id
        return $this->belongsTo(Organisation::class);
    }


}
