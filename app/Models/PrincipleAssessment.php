<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;
use Venturecraft\Revisionable\Revisionable;
use Venturecraft\Revisionable\RevisionableTrait;

class PrincipleAssessment extends Model
{
    use CrudTrait;
    use RevisionableTrait;

    protected $guarded = ['id', 'principle_id', 'assessment_id'];

    public $table = 'principle_assessment';

    // needed for revisionable
    public function identifiableName(): string
    {
        // MAYBE: Adapt this to include the number of assessment for projects with multiple assessments?
        return $this->assessment->project->name
            . ' - ' . $this->principle->name;
    }


    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function principle(): BelongsTo
    {
        return $this->belongsTo(Principle::class);
    }

    public function scoreTags(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class)
            ->withPivot('assessment_id');
    }

    public function customScoreTags(): HasMany
    {
        return $this->hasMany(CustomScoreTag::class);
    }
}
