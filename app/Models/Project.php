<?php

namespace App\Models;

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

class Project extends Model
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
            logger("Project (" . $project->id . ") - creating(): " . count($project->regions));

            if (is_null($project->code)) {
                $org_project_number = Project::where('organisation_id', $project->organisation_id)->count() + 1;

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
            logger("Project (" . $project->id . ") - created(): " . count($project->regions));

            $assessment = Assessment::create(['project_id' => $project->id]);
            $assessment->redLines()->sync(RedLine::all()->pluck('id')->toArray());
            $assessment->principles()->sync(Principle::all()->pluck('id')->toArray());
            $assessment->additionalCriteria()->sync($project->organisation->additionalCriteria->pluck('id')->toArray());
        });

        /************/

        static::updating(function (Project $project) {
            logger("Project (" . $project->id . ") - updating(): " . count($project->regions));
        });

        static::updated(function (Project $project) {
            logger("Project (" . $project->id . ") - updated(): " . count($project->regions));
        });

        static::saved(function (Project $project) {
            logger("Project (" . $project->id . ") - saved(): " . count($project->regions));
            foreach ($project->regions as $region) {
                logger($region->name);
            }
        });

        static::retrieved(function (Project $project) {
            logger("Project (" . $project->id . ") - retrieved(): " . count($project->regions));
            foreach ($project->regions as $region) {
                logger($region->name);
            }
        });

        /************/

        static::saving(function (Project $project) {
            logger("Project (" . $project->id . ") - saving(): " . count($project->regions));

            // in case the exchange_rate has changed, re-calculate the budget_org
           $project->budget_org = $project->budget * $project->exchange_rate;
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
}
