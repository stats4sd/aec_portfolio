<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentCriteria extends Model
{
    use CrudTrait;

    protected $table = 'assessment_criteria';
    protected $guarded = ['id'];


    public function institution(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
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
