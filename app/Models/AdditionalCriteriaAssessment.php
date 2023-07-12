<?php

namespace App\Models;

use App\Http\Controllers\AdditionalAssessmentController;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Venturecraft\Revisionable\RevisionableTrait;

class AdditionalCriteriaAssessment extends Model
{

    use CrudTrait;
    use RevisionableTrait;

    protected $table = 'additional_criteria_assessment';
    protected $guarded = ['id'];

    public function identifiableName(): string
    {
        // MAYBE: Adapt this to include the number of assessment for projects with multiple assessments?
        return $this->assessment->project->name
            . ' - ' . $this->additionalCriteria->name;
    }


    public function getCompleteAttribute(): bool
    {
        return $this->is_na || $this->rating !== null;
    }


    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function additionalCriteria(): BelongsTo
    {
        return $this->belongsTo(AdditionalCriteria::class);
    }

    // for harmonised UI (principle === additional criteria)
    public function principle(): BelongsTo
    {
        return $this->belongsTo(AdditionalCriteria::class, 'additional_criteria_id');
    }

    public function scoreTags(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalCriteriaScoreTag::class)
            ->withPivot('assessment_id');
    }

    public function customScoreTags(): HasMany
    {
        return $this->hasMany(AdditionalCriteriaCustomScoreTag::class);
    }

}
