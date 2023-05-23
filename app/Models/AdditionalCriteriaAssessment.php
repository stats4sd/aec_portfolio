<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdditionalCriteriaAssessment extends Model
{
    protected $table = 'additional_criteria_assessment';
    protected $guarded = ['id'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function additionalCriteria(): BelongsTo
    {
        return $this->belongsTo(AdditionalCriteria::class);
    }

    public function additionalCriteriaScoreTags(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalCriteriaScoreTag::class)
            ->withPivot('assessment_id');
    }

    public function additionalCriteriaCustomScoreTags(): HasMany
    {
        return $this->hasMany(AdditionalCriteriaCustomScoreTag::class);
    }

}
