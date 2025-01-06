<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Services\XeCurrency;
use App\Enums\AssessmentStatus;
use App\Enums\GeographicalReach;
use Illuminate\Support\Facades\Auth;
use App\Services\OrganisationService;
use App\Services\FreeCurrencyApiHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Admin\Operations\RedlineOperation;

class
Project extends Model
{
    use CrudTrait, HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($project) {
            if (is_null($project->code)) {
                // when counting total number of project of an organisation, include soft-deleted projects
                // to avoid generating a duplicated project code
                $org_project_number = Project::withTrashed()->where('organisation_id', $project->organisation_id)->count() + 1;

                $org_name = $project->organisation->name;
                $org_name_words = explode(' ', $org_name);
                $org_initials = "";
                foreach ($org_name_words as $word) {
                    $org_initials = $org_initials . $word[0];
                }

                $project->code = Str::upper($org_initials) . $org_project_number;
            }
        });

        static::created(function (Project $project) {
            $assessment = Assessment::create(['project_id' => $project->id]);
            $assessment->redLines()->sync(RedLine::all()->pluck('id')->toArray());
            $assessment->principles()->sync(Principle::all()->pluck('id')->toArray());
            $assessment->additionalCriteria()->sync($project->organisation->additionalCriteria->pluck('id')->toArray());
        });

        static::saved(function (Project $project) {

            // in case the exchange_rate has changed, re-calculate the budget_org
            $project->budget_org = $project->budget * $project->exchange_rate;
            $project->saveQuietly();
        });

        static::addGlobalScope('organisation', function (Builder $builder) {
            $builder->where('organisation_id', Session::get('selectedOrganisationId'));
        });
    }

    public function fundingSources(): HasMany
    {
        return $this->hasMany(FundingSource::class);
    }

    public function continents()
    {
        return $this->belongsToMany(Continent::class);
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function getLatestAssessmentAttribute()
    {
        return $this->assessments->last();
    }

    public function getLatestAssessmentStatusAttribute()
    {
        return $this->assessments->last()?->assessment_status;
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class)
            ->withoutGlobalScope('organisation');
    }

    public function customScoreTags()
    {
        return $this->hasMany(CustomScoreTag::class);
    }

    public function initiativeCategory(): BelongsTo
    {
        return $this->belongsTo(InitiativeCategory::class);
    }

    public function aeBudget(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->latest_assessment_status === 'Principles Complete' || $this->latest_assessment_status === 'Additional Criteria Complete') {
                    return $this->budget_org * $this->latest_assessment->overall_score / 100;
                }
                return 0;
            }
        );
    }

    // computed field to show budget with thousand separator
    public function displayBudget(): Attribute
    {
        return new Attribute(
            get: fn() => number_format($this->budget),
        );
    }

    // computed field to show budget_eur with thousand separator
    public function displayBudgetEur(): Attribute
    {
        return new Attribute(
            get: fn() => number_format($this->budget_eur),
        );
    }

    // computed field to show budget org with thousand separator
    public function displayBudgetOrg(): Attribute
    {
        return new Attribute(
            get: fn() => number_format($this->budget_org),
        );
    }
}
