<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PrincipleAssessment extends Model
{

    public $incrementing = true;
    public $primaryKey = 'id';
    public $table = 'principle_assessment';

    public function principle()
    {
        return $this->belongsTo(Principle::class);
    }

    public function scoreTags()
    {
        return $this->belongsToMany(ScoreTag::class);
    }

    public function customScoreTags()
    {
        return $this->hasMany(CustomScoreTag::class);
    }
}
