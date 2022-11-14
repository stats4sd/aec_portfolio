<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedLine extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        parent::booted();

        static::created(function($redLine) {
            $redLine->projects()->sync(Project::all()->pluck('id')->toArray());
        });

    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withPivot([
                'value'
            ]);
    }

    public function failingProjects()
    {
        return $this->belongsToMany(Project::class)
            ->wherePivot('value', 1);
    }
}
