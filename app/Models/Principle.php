<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Principle extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'can_be_na' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
            //->using(PrincipleProject::class);
    }

    // TODO: add function for pivot
    public function assessments()
    {
        return $this->belongsToMany(Assessment::class)
            ->withPivot([
                'rating',
                'rating_comment',
                'is_na',
            ]);
            //->using(PrincipleProject::class);
    }

    public function principleAssessments()
    {
        return $this->hasMany(PrincipleAssessment::class);
    }

    public function scoreTags()
    {
        return $this->hasMany(ScoreTag::class);
    }

    public function customScoreTags()
    {
        return $this->hasMany(CustomScoreTag::class);
    }
}
