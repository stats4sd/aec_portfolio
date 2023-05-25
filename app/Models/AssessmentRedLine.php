<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Venturecraft\Revisionable\RevisionableTrait;

class AssessmentRedLine extends Model
{
    use RevisionableTrait;

    protected $table = 'assessment_red_line';

    public function identifiableName(): string
    {
        // MAYBE: Adapt this to include the number of assessment for projects with multiple assessments?

        return $this->assessment->project->name
            . ' - ' . $this->redLine->name;
    }

    public function redLine(): BelongsTo
    {
        return $this->belongsTo(RedLine::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
