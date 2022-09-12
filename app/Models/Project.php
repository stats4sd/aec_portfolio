<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function ($project) {
            $project->redLines()->sync(RedLine::all()->pluck('id')->toArray());

            dump(Principle::all()->pluck('id')->toArray());
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

    public function principles()
    {
        return $this->belongsToMany(Principle::class)
            ->withPivot([
                'rating',
                'rating_comment',
            ]);
            //->using(PrincipleProject::class);
    }

    public function principleProjects()
    {
        return  $this->belongsTo(PrincipleProject::class);
    }


}
