<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use App\Enums\GeographicalReach;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Project extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    // TODO: this is no longer necessary as OrganisationController now check for passed project by referring to project latest assessment
    // protected $appends = ['overall_score'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($project) {
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

        // DONE - TODO: change table name and column name to see whether assessment_id will have value
        static::created(function ($project) {
            $assessment = Assessment::create(['project_id' => $project->id]);
            $assessment->redLines()->sync(RedLine::all()->pluck('id')->toArray());
            $assessment->principles()->sync(Principle::all()->pluck('id')->toArray());
        });

        static::addGlobalScope('organisation', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            if (Auth::user()->hasRole('Site Admin')) {
                return;
            }
            $builder->whereIn('organisation_id', Auth::user()->organisations->pluck('id')->toArray());
        });
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

    public function latestAssessment() {
        return $this->assessments->last();
    }

    public function getLatestAssessmentStatusAttribute() {
        return $this->assessments->last()->assessment_status?->value;
    }

    public
    function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public
    function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public
    function customScoreTags()
    {
        return $this->hasMany(CustomScoreTag::class);
    }

}