<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Venturecraft\Revisionable\Revision;
use Venturecraft\Revisionable\Revisionable;
use Venturecraft\Revisionable\RevisionableTrait;

class Assessment extends Model
{
    use CrudTrait;

    protected $table = 'assessments';
    protected $guarded = ['id'];


    protected $appends = [
        'overall_score',
        'assessment_status',
    ];

    protected static function booted()
    {
        static::creating(function ($assessment) {

            $org_has_additional_criteria = $assessment->project->organisation->has_additional_criteria;

            if ($org_has_additional_criteria) {
                $assessment->additional_status = "Not Started";
            }
        });

    }

    public function getAssessmentStatusAttribute(): string
    {
        // if redlines are not complete, use that status
        if ($this->redline_status !== AssessmentStatus::Complete->value) {
            return "Red Flags " . $this->redline_status;
        }

        if (
            // if principle status is not complete...
            $this->principle_status !== AssessmentStatus::Complete->value ||
            // or if additional criteria are not relevant...
            !$this->project->organisation->has_additional_criteria || $this->additionalCriteria->count() === 0
        ) {
            return "Principles " . $this->principle_status;
        }

        return "Additional Criteria " . $this->additional_status;
    }

    // determine if there are revisions related to this assessment
    public function hasRevisions(): bool
    {
        return $this->assessmentRedLines->some(fn(AssessmentRedLine $entry) => count($entry->revisionHistory))
            || $this->principleAssessments->some(fn(PrincipleAssessment $entry) => count($entry->revisionHistory))
            || $this->additionalCriteriaAssessment->some(fn(AdditionalCriteriaAssessment $entry) => count($entry->revisionHistory));
    }

    public function getRevisionHistoryAttribute()
    {
        return
            collect([
                $this->assessmentRedLines->reduce(fn($carry, $item) => $carry->merge($this->appendExtrasToRevision($item, 'redLine')), collect([])),
                $this->principleAssessments->reduce(fn($carry, $item) => $carry->merge($this->appendExtrasToRevision($item, 'principle')), collect([])),
                $this->additionalCriteriaAssessment->reduce(fn($carry, $item) => $carry->merge($this->appendExtrasToRevision($item, 'additionalCriteria')), collect([])),
            ])
                ->flatten();
    }

    public function appendExtrasToRevision($item, $relation)
    {
        return $item->revisionHistory->map(function (Revision $history) use ($item, $relation) {
            $history->relation = Str::lower(Arr::join(Str::ucsplit($relation), ' '));
            $history->linkedItemName = $item->$relation?->name;

            return $history;
        });
    }


    public function project()
    {
        return $this->belongsTo(Project::class)
         ->withoutGlobalScope('organisation');
    }

    public function customScoreTags(): HasMany
    {
        return $this->hasMany(CustomScoreTag::class);
    }

    public function additionalCriteriaCustomScoreTag(): HasMany
    {
        return $this->hasMany(AdditionalCriteriaCustomScoreTag::class);
    }

    public function redLines()
    {
        return $this->belongsToMany(RedLine::class)
            ->withPivot([
                'value'
            ]);
    }

    public function assessmentRedLines(): HasMany
    {
        return $this->hasMany(AssessmentRedLine::class);
    }

    public function completedRedlines(): BelongsToMany
    {
        return $this->belongsToMany(RedLine::class)
            ->wherePivot('value', '!=', null);
    }

    // relationship to get Failing redlines
    public function failingRedlines(): BelongsToMany
    {
        return $this->belongsToMany(RedLine::class)->wherePivot('value', 1);
    }


    public function principles(): BelongsToMany
    {
        return $this->belongsToMany(Principle::class, 'principle_assessment', 'assessment_id')
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
    }

    public function principleAssessments(): HasMany
    {
        return $this->hasMany(PrincipleAssessment::class);
    }

    public function getTotalPossibleAttribute(): ?int
    {
        if ($this->failingRedlines()->count() > 0) {
            return 0;
        }

        if ($this->principle_status === AssessmentStatus::Complete->value) {
            $principles = $this->principles;

            $nonNaPrinciples = $principles->filter(fn($pr) => !$pr->pivot->is_na);

            return $nonNaPrinciples->count() * 2;
        }

        return null;
    }


    public function getTotalAttribute(): ?float
    {
        if ($this->failingRedlines()->count() > 0) {
            return 0;
        }

        if ($this->principle_status === AssessmentStatus::Complete->value) {
            $principles = $this->principles;

            return $principles->filter(fn($pr) => !$pr->pivot->is_na)->sum(fn($pr) => $pr->pivot->rating);

        }

        return null;
    }

    public function getOverallScoreAttribute(): ?int
    {
        if ($this->failingRedlines->count() > 0) {
            return 0;
        }

        if ($this->principle_status === AssessmentStatus::Complete->value) {
            $principles = $this->principles;

            $nonNaPrinciples = $principles->filter(fn($pr) => !$pr->pivot->is_na);

            $totalPossible = $nonNaPrinciples->count() * 2;

            $total = $nonNaPrinciples->sum(fn($pr) => $pr->pivot->rating);

            return round($total / $totalPossible * 100, 0);
        }

        return null;
    }


    // Custom Assessment Criteria
    public function additionalCriteria(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalCriteria::class, 'additional_criteria_assessment')
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na'
            ]);
    }

    public function additionalCriteriaAssessment(): HasMany
    {
        return $this->hasMany(AdditionalCriteriaAssessment::class);
    }


    // Custom relationships to load scoreTags filtered by each of the 13 principles
    // hard-coded principles, so careful if we change our definition of AE!
    public function scoreTags1(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 1);
    }

    public function scoreTags2(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 2);
    }

    public function scoreTags3(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 3);
    }

    public function scoreTags4(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 4);
    }

    public function scoreTags5(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 5);
    }

    public function scoreTags6(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 6);
    }

    public function scoreTags7(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 7);
    }

    public function scoreTags8(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 8);
    }

    public function scoreTags9(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 9);
    }

    public function scoreTags10(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 10);
    }

    public function scoreTags11(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 11);
    }

    public function scoreTags12(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 12);
    }

    public function scoreTags13(): BelongsToMany
    {
        return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
            ->withPivot('principle_assessment_id')
            ->where('principle_id', 13);
    }

    // Custom relationships to load customScoreTags filtered by each of the 13 principles
    // hard-coded principles, so careful if we change our definition of AE!
    public function getCustomScoreTags1Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 1)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags2Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 2)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags3Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 3)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags4Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 4)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags5Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 5)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags6Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 6)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags7Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 7)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags8Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 8)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags9Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 9)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags10Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 10)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags11Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 11)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags12Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 12)->first()->customScoreTags->toArray();
    }

    public function getCustomScoreTags13Attribute(): array
    {
        return $this->principleAssessments()->where('principle_id', 13)->first()->customScoreTags->toArray();
    }


}
