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

    
    public function redLines()
    {
        dump("Assessment.redLines()");

        return $this->belongsToMany(RedLine::class, 'project_red_line', 'assessment_id')
            ->withPivot([
                'value'
            ]);
    }

    public function completedRedlines()
    {
        dump("Assessment.completedRedlines()");

        return $this->belongsToMany(RedLine::class, 'project_red_line', 'assessment_id')
            ->wherePivot('value', '!=', null);
    }

    // relationship to get Failing redlines
    public function failingRedlines()
    {
        dump("Assessment.failingRedlines()");

        return $this->belongsToMany(RedLine::class, 'project_red_line', 'assessment_id')->wherePivot('value', 1);
    }

    
    public function principles()
    {
        dump("Assessment.principles()");

        return $this->belongsToMany(Principle::class, 'principle_project', 'assessment_id')
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
    }

    public function principleProjects()
    {
        dump("Assessment.principleProjects()");

        return $this->hasMany(PrincipleProject::class);
    }

    public function getTotalPossibleAttribute()
    {
        dump("Assessment.getTotalPossibleAttribute()");

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
        dump("Assessment.getTotalAttribute()");

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
        dump("Assessment.getOverallScoreAttribute()");

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
