<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class Country extends Model
{
    use CrudTrait, BelongsToThrough;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'countries';
    public $keyType = 'string';
    protected $guarded = [];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function continent()
    {
        return $this->belongsToThrough(Continent::class, Region::class);
    }

}
