<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Principle extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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

    public function principleProjects()
    {
        return $this->hasMany(PrincipleProject::class);
    }

    public function scoreTags()
    {
        return $this->hasMany(ScoreTag::class);
    }
}
