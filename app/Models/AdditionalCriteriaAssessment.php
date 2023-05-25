<?php

namespace App\Models;

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
