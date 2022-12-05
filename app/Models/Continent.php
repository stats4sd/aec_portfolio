<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Continent extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'continents';
    protected $guarded = [];
    public $keyType = 'string';

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function countries()
    {
        return $this->hasManyThrough(Country::class, Region::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

}
