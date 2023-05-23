<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PrincipleAssessment extends Model
{

    public $incrementing = true;
    public $primaryKey = 'id';
    public $table = 'principle_assessment';

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
