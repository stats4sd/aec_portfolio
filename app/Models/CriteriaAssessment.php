<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriteriaAssessment extends Model
{
    protected $guarded = ['id'];

    public function assessmentCriteria(): BelongsTo
    {
        return $this->belongsTo(AssessmentCriteria::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
