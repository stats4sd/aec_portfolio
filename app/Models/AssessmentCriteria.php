<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Testing\Fluent\Concerns\Has;

class AssessmentCriteria extends Model
{
    use CrudTrait, HasFactory;

    protected $table = 'assessment_criteria';
    protected $guarded = ['id'];

    protected static function booted()
    {
        parent::booted();


        // on creation, make sure that the new AssessmentCriterion is included in all exsiting projects (latest assessment only).
        static::created(static function(AssessmentCriteria $entry) {
           foreach($entry->institution->projects as $project) {
               $latestAssessment = $project->assessments->last();
               $latestAssessment->assessmentCriteria()->attach($entry->id);
           }
        });
    }


    public function institution(): BelongsTo
    {
        return $this->belongsTo(Organisation::class,  'organisation_id');
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'criteria_assessment')
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
    }

    public function criteriaAssessment(): HasMany
    {
        return $this->hasMany(CriteriaAssessment::class);
    }


}
