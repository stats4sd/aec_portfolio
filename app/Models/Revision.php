<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Venturecraft\Revisionable\Revisionable;

class Revision extends Revisionable
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
        // I did some searches in google and our projects, I cannot find example for reference...
        // Um... cannot access the corresponding model's assessment model directly...
        // logger($this->assessment->project);

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
