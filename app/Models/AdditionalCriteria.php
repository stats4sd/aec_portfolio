<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Testing\Fluent\Concerns\Has;

class AdditionalCriteria extends Model
{
    use CrudTrait, HasFactory;

    protected $table = 'additional_criteria';
    protected $guarded = ['id'];

    protected static function booted()
    {
        parent::booted();


        // on creation, make sure that the new AssessmentCriterion is included in all exsiting projects (latest assessment only).
        static::created(static function(AdditionalCriteria $entry) {
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
        return $this->belongsToMany(Assessment::class, 'additional_criteria_assessment')
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
    }

    public function additionalCriteriaAssessment(): HasMany
    {
        return $this->hasMany(AdditionalCriteriaAssessment::class);
    }

    public function assessmentCriteriaScoreTags(): HasMany
    {
        return $this->hasMany(AssessmentCriteriaScoreTags);
    }


}
