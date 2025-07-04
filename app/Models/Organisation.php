<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Enums\AssessmentStatus;
use App\Mail\AddMemberConfirmed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\OrganisationRequest;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'agreement_signed_at' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope('owned', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            // if the logged in user is the default Site Admin user account with ID 1, he is allowed to see all organisations
            // Note: this is modified for BrowserShot as it uses account support@stats4sd.org for authentication to generate page content for PDF file
            if (Auth::user()->id == 1) {
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
            if ($organisation->has_additional_criteria) {
                $organisation->assessments()->where('additional_status', AssessmentStatus::Na->value)
                    ->orWhere('additional_status', null)
                    ->update(['additional_status' => AssessmentStatus::NotStarted->value]);
            }
        });

        // Add model_has_roles records for site admins and site managers when a new institution is created
        static::created(function ($item) {
            // add role for all Site Admin users
            $siteAdmins = DB::select('SELECT DISTINCT model_id, role_id FROM model_has_roles WHERE role_id = 1');

            foreach ($siteAdmins as $siteAdmin) {
                $insertSql = "INSERT INTO model_has_roles VALUES (1, 'App\\\Models\\\User', " . $siteAdmin->model_id . ", " . $item->id . ")";
                DB::statement($insertSql);
            }

            // add role for all Site Manager users
            $siteManagers = DB::select('SELECT DISTINCT model_id, role_id FROM model_has_roles WHERE role_id = 2');

            foreach ($siteManagers as $siteManager) {
                $insertSql = "INSERT INTO model_has_roles VALUES (2, 'App\\\Models\\\User', " . $siteManager->model_id . ", " . $item->id . ")";
                DB::statement($insertSql);
            }
        });
    }


    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)
            ->withoutGlobalScopes(['organisation']);
    }

    public function tempProjects(): HasMany
    {
        return $this->hasMany(TempProject::class)
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
        return $this->belongsToMany(User::class, 'organisation_members')
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', 'Institutional Admin');
            });
    }

    public function editors()
    {
        return $this->belongsToMany(User::class, 'organisation_members')
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', 'Institutional Assessor');
            });
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'organisation_members')
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', 'Institutional Member');
            });
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

            // if email is empty, skip to next email
            if ($email == null || $email == '') {
                continue;
            }

            // check whether email address belongs to existing user
            $user = User::where('email', $email)->first();

            // for new user, send email invitation
            if ($user == null) {

                // create invite model, it will trigger to send email invitation automatically
                $invite = $this->invites()->create([
                    'email' => $email,
                    'inviter_id' => auth()->user()->id,
                    'token' => Str::random(24),
                ]);

                // create role_invites record with same token from corresponding invites record
                // P.S. tried to do the same by RoleInvite::create() but another invitation email with role will be sent
                // To avoid sending the additional invitation email regarding role, insert a role_invites record via DB facade directly
                DB::insert(
                    'insert into role_invites (email, role_id, inviter_id, token, created_at, updated_at) values (?, ?, ?, ?, NOW(), NOW())',
                    [$email, $roleId, auth()->user()->id, $invite->token]
                );

                // for existing user, send email confirmation instead of email invitation
            } else {

                // check whether this existing user is already in this institution
                if (!$this->users->contains($user)) {

                    // send email confirmation
                    Mail::to($email)->send(new AddMemberConfirmed(auth()->user()->name, auth()->user()->email, $this->name));

                    // create role_invites record for recording purpose
                    DB::insert(
                        'insert into role_invites (email, role_id, inviter_id, token, is_confirmed, created_at, updated_at) values (?, ?, ?, ?, ?, NOW(), NOW())',
                        [$email, $roleId, auth()->user()->id, 'N/A', 1]
                    );

                    // add organisation_member record, existing user will belong to this institution immediately
                    OrganisationMember::create([
                        'user_id' => $user->id,
                        'organisation_id' => $this->id,
                    ]);

                    // add model_has_roles records, existing user will have a role to this institution immediately
                    DB::insert(
                        'insert into model_has_roles (role_id, model_type, model_id, organisation_id) values (?, ?, ?, ?)',
                        [$roleId, 'App\\Models\\User', $user->id, $this->id]
                    );
                }
            }
        }
    }

    public function institutionType(): BelongsTo
    {
        return $this->belongsTo(InstitutionType::class);
    }

    public function signee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signee_id');
    }

    public function getFundingFlowAnalysisAttribute()
    {
        return $this->contributes_to_funding_flow == 1 ? 'Yes' : 'No';
    }
}
