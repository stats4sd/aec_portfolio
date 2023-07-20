<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Organisation extends Model
{
    use CrudTrait, HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'organisations';
    protected $guarded = ['id'];

    protected $casts = [
        'has_additional_criteria' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('owned', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            if (Auth::user()?->can('view institutions')) {
                return;
            }

            $builder->whereHas('users', function ($query) {
                $query->where('users.id', Auth::id());
            });
        });

        // If assessment_criteria are used, set additional_criteria status
        static::saved(function (Organisation $organisation) {
            if($organisation->has_additional_criteria) {
                $organisation->assessments()->where('additional_status', AssessmentStatus::Na->value)
                    ->orWhere('additional_status', null)
                    ->update(['additional_status' => AssessmentStatus::NotStarted->value]);
            }
        });
    }


    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)
            ->withoutGlobalScopes(['organisation']);
    }

    public function assessments(): HasManyThrough
    {
        return $this->hasManyThrough(Assessment::class, Project::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_members')->withPivot('role');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'organisation_members')->withPivot('role')->wherePivot('role', '=', 'Site Admin');
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

    public function additionalCriteria(): HasMany
    {
        return $this->hasMany(AdditionalCriteria::class);
    }

    public function removalRequests()
    {
        return $this->hasMany(RemovalRequest::class);
    }


    public function invites()
    {
        return $this->hasMany(Invite::class);
    }


    public function sendInvites($emails, $roleId)
    {
        foreach ($emails as $email) {
            $invite = $this->invites()->create([
                'email' => $email,
                'inviter_id' => auth()->user()->id,
                'token' => Str::random(24),
            ]);

            // create role_invites record with same token from corresponding invites record
            // P.S. tried to do the same by RoleInvite::create() but another invitation email with role will be sent
            // To avoid sending the additional invitation email regarding role, insert a role_invites record via DB facade directly
            DB::insert('insert into role_invites (email, role_id, inviter_id, token, created_at, updated_at) values (?, ?, ?, ?, NOW(), NOW())',
                [$email, $roleId, auth()->user()->id, $invite->token]);
        }
    }

    public function institutionType(): BelongsTo
    {
        return $this->belongsTo(InstitutionType::class);
    }
}
