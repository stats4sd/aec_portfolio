<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'regions';
    protected $guarded = [];
    public $keyType = 'string';

    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function continent()
    {
        return $this->belongsTo(Continent::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
