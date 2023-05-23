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

    public function completedRedlines()
    {
        return $this->belongsToMany(RedLine::class)
            ->wherePivot('value', '!=', null);
    }

    // relationship to get Failing redlines
    public function failingRedlines()
    {
        return $this->belongsToMany(RedLine::class)->wherePivot('value', 1);
    }


    public function principles()
    {
        return $this->belongsToMany(Principle::class, 'principle_assessment', 'assessment_id')
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
    }

    public function principleAssessments()
    {
        return $this->hasMany(PrincipleAssessment::class);
    }

    public function getTotalPossibleAttribute()
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


    public function getTotalAttribute()
    {
        if ($this->failingRedlines()->count() > 0) {
            return 0;
        }

        if ($this->assessment_status === AssessmentStatus::Complete) {
            $principles = $this->principles;

            $nonNaPrinciples = $principles->filter(fn($pr) => !$pr->pivot->is_na);

            return $nonNaPrinciples->sum(fn($pr) => $pr->pivot->rating);

        }
    }

    public function getOverallScoreAttribute()
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


        // Custom relationships to load scoreTags filtered by each of the 13 principles
        // hard-coded principles, so careful if we change our definition of AE!
        public function scoreTags1()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 1);
        }

        public function scoreTags2()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 2);
        }

        public function scoreTags3()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 3);
        }

        public function scoreTags4()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 4);
        }

        public function scoreTags5()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 5);
        }

        public function scoreTags6()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 6);
        }

        public function scoreTags7()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 7);
        }

        public function scoreTags8()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 8);
        }

        public function scoreTags9()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 9);
        }

        public function scoreTags10()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 10);
        }

        public function scoreTags11()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 11);
        }

        public function scoreTags12()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 12);
        }

        public function scoreTags13()
        {
            return $this->belongsToMany(AdditionalCriteriaScoreTag::class, 'principle_assessment_score_tag', 'assessment_id', 'score_tag_id')
                ->withPivot('principle_assessment_id')
                ->where('principle_id', 13);
        }

        // Custom relationships to load customScoreTags filtered by each of the 13 principles
        // hard-coded principles, so careful if we change our definition of AE!
        public function getCustomScoreTags1Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 1)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags2Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 2)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags3Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 3)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags4Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 4)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags5Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 5)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags6Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 6)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags7Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 7)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags8Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 8)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags9Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 9)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags10Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 10)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags11Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 11)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags12Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 12)->first()->customScoreTags->toArray();
        }

        public function getCustomScoreTags13Attribute()
        {
            return $this->principleAssessments()->where('principle_id', 13)->first()->customScoreTags->toArray();
        }


}
