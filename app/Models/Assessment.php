<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use CrudTrait;

    protected $table = 'assessments';
    protected $guarded = ['id'];

    protected $casts = [
        'assessment_status' => AssessmentStatus::class,
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customScoreTags()
    {
        return $this->hasMany(CustomScoreTag::class);
    }

    public function redLines()
    {
        return $this->belongsToMany(RedLine::class)
            ->withPivot([
                'value'
            ]);
    }

    public function assessmentRedLine(): HasMany
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

    public function getTotalPossibleAttribute(): int
    {
        if ($this->failingRedlines()->count() > 0) {
            return 0;
        }

        if ($this->assessment_status === AssessmentStatus::Complete) {
            $principles = $this->principles;

            $nonNaPrinciples = $principles->filter(fn($pr) => !$pr->pivot->is_na);

            return $nonNaPrinciples->count() * 2;
        }
    }


    public function getTotalAttribute(): int
    {
        if ($this->failingRedlines()->count() > 0) {
            return 0;
        }

        if ($this->assessment_status === AssessmentStatus::Complete) {
            $principles = $this->principles;

            return $principles->filter(fn($pr) => !$pr->pivot->is_na)->sum(fn($pr) => $pr->pivot->rating);

        }
    }

    public function getOverallScoreAttribute(): ?int
    {
        if ($this->failingRedlines()->count() > 0) {
            return 0;
        }

        if ($this->assessment_status === AssessmentStatus::Complete) {
            $principles = $this->principles;

            $nonNaPrinciples = $principles->filter(fn($pr) => !$pr->pivot->is_na);

            $totalPossible = $nonNaPrinciples->count() * 2;

            $total = $nonNaPrinciples->sum(fn($pr) => $pr->pivot->rating);

            return round($total / $totalPossible * 100, 1);
        }

        return null;
    }




        // Custom relationships to load scoreTags filtered by each of the 13 principles
        // hard-coded principles, so careful if we change our definition of AE!
        public
        function scoreTags1(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 1);
        }

        public
        function scoreTags2(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 2);
        }

        public
        function scoreTags3(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 3);
        }

        public
        function scoreTags4(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 4);
        }

        public
        function scoreTags5(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 5);
        }

        public
        function scoreTags6(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 6);
        }

        public
        function scoreTags7(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 7);
        }

        public
        function scoreTags8(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 8);
        }

        public
        function scoreTags9(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 9);
        }

        public
        function scoreTags10(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 10);
        }

        public
        function scoreTags11(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 11);
        }

        public
        function scoreTags12(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 12);
        }

        public
        function scoreTags13(): BelongsToMany
        {
            return $this->belongsToMany(ScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 13);
        }

        // Custom relationships to load customScoreTags filtered by each of the 13 principles
        // hard-coded principles, so careful if we change our definition of AE!
        public
        function getCustomScoreTags1Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 1)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags2Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 2)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags3Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 3)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags4Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 4)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags5Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 5)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags6Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 6)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags7Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 7)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags8Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 8)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags9Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 9)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags10Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 10)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags11Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 11)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags12Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 12)->first()->customScoreTags->toArray();
        }

        public
        function getCustomScoreTags13Attribute(): array
        {
            return $this->principleAssessments()->where('principle_id', 13)->first()->customScoreTags->toArray();
        }


}
