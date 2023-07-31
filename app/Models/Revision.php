<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revision extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'revisions';
    protected $guarded = ['id'];

    protected static function booted()
    {
        // TODO: add global scope to filter by currently selected organisation.

        // Unlike portfolio table and project table, there is no organisation_id column in revisions table.
        // Question: How do we check this revision record is related to an organisation indirectly...
        // Red flag / Principle / Additional Criteria -> assessment -> project -> organisation
        // static::addGlobalScope('organisation',  function(Builder $query) {
        //     $query->where('organisation_id', Session::get('selectedOrganisationId'));
        // });

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

    public function getItemTypeAttribute() {
        if ($this->revisionable_type == 'App\Models\AssessmentRedLine') {
            return 'Red Flag';
        } else if ($this->revisionable_type == 'App\Models\PrincipleAssessment') {
            return 'Principle';
        } else if ($this->revisionable_type == 'App\Models\AdditionalCriteriaAssessment') {
            return 'Additional Criteria'; 
        }
    }

    public function getItemAttribute() {
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

    public function getProjectAttribute() {
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

    public function getProjectIdAttribute() {
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
