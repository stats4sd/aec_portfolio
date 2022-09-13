<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['overall_score'];

    protected $casts = [
        'assessment_status' => AssessmentStatus::class,
    ];

    protected static function booted()
    {
        static::created(function ($project) {
            $project->redLines()->sync(RedLine::all()->pluck('id')->toArray());

            $project->principles()->sync(Principle::all()->pluck('id')->toArray());
        });
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
        return $this->belongsToMany(Principle::class)
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
    }

    public function principleProjects()
    {
        return  $this->belongsTo(PrincipleProject::class);
    }

    public function getOverallScoreAttribute()
    {
        if($this->failingRedlines()->count() > 0) {
            return 0;
        }

        if($this->assessment_status === AssessmentStatus::Complete) {
            $principles = $this->principles;

            $nonNaPrinciples = $principles->filter(fn($pr) => ! $pr->pivot->is_na );

            $totalPossible = $nonNaPrinciples->count() * 2;

            $total = $nonNaPrinciples->sum(fn($pr) => $pr->pivot->rating );

            return  $total / $totalPossible * 100;

        }


        return null;
    }


}
