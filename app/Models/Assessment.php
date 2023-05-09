<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assessment extends Model
{
    use CrudTrait;
    use HasFactory;

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

    public function principleProjects()
    {
        return $this->hasMany(PrincipleProject::class);
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

}
