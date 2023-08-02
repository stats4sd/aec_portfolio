<?php

namespace App\Models;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Revision extends \Venturecraft\Revisionable\Revision
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public $table = 'revisions';
    protected $guarded = ['id'];

    protected static function booted()
    {
        // add a global scope to only return entries for the selected organisation
        // assumption: all 'revisionable' data models are related to the Assessment model
        static::addGlobalScope('organisation', function (Builder $query) {
            $query->whereHas('revisionable', function (Builder $query) {
                $query->whereHas('assessment', function (Builder $query) {
                    $query->whereHas('project', function (Builder $query) {
                        $query->where('projects.organisation_id', Session::get('selectedOrganisationId'));
                    });
                });
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getItemTypeAttribute()
    {
        if ($this->revisionable_type == 'App\Models\AssessmentRedLine') {
            return 'Red Flag';
        } else if ($this->revisionable_type == 'App\Models\PrincipleAssessment') {
            return 'Principle';
        } else if ($this->revisionable_type == 'App\Models\AdditionalCriteriaAssessment') {
            return 'Additional Criteria';
        }
    }

    public function getItemAttribute()
    {
        if ($this->revisionable_type == 'App\Models\AssessmentRedLine') {
            $assessmentRedLine = AssessmentRedLine::find($this->revisionable_id);
            return $assessmentRedLine->redLine->name;

        } else if ($this->revisionable_type == 'App\Models\PrincipleAssessment') {
            $principleAssessment = PrincipleAssessment::find($this->revisionable_id);
            return $principleAssessment->principle->name;

        } else if ($this->revisionable_type == 'App\Models\AdditionalCriteriaAssessment') {
            $additionalCriteriaAssessment = AdditionalCriteriaAssessment::find($this->revisionable_id);
            return $additionalCriteriaAssessment->additionalCriteria->name;

        }
    }

    public function getProjectAttribute()
    {
        if ($this->revisionable_type == 'App\Models\AssessmentRedLine') {
            $assessmentRedLine = AssessmentRedLine::find($this->revisionable_id);
            return $assessmentRedLine->assessment->project;

        } else if ($this->revisionable_type == 'App\Models\PrincipleAssessment') {
            $principleAssessment = PrincipleAssessment::find($this->revisionable_id);
            return $principleAssessment->assessment->project;

        } else if ($this->revisionable_type == 'App\Models\AdditionalCriteriaAssessment') {
            $additionalCriteriaAssessment = AdditionalCriteriaAssessment::find($this->revisionable_id);
            return $additionalCriteriaAssessment->assessment->project;
        }
    }

    public function getProjectIdAttribute()
    {
        if ($this->revisionable_type == 'App\Models\AssessmentRedLine') {
            $assessmentRedLine = AssessmentRedLine::find($this->revisionable_id);
            return $assessmentRedLine->assessment->project->id;

        } else if ($this->revisionable_type == 'App\Models\PrincipleAssessment') {
            $principleAssessment = PrincipleAssessment::find($this->revisionable_id);
            return $principleAssessment->assessment->project->id;

        } else if ($this->revisionable_type == 'App\Models\AdditionalCriteriaAssessment') {
            $additionalCriteriaAssessment = AdditionalCriteriaAssessment::find($this->revisionable_id);
            return $additionalCriteriaAssessment->assessment->project->id;
        }
    }

}
