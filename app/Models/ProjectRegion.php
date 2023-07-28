<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectRegion extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'project_region';
    protected $guarded = [];
    public $keyType = 'string';


    protected static function booted()
    {
        /************/

        static::updating(function (ProjectRegion $projectRegion) {
            logger("ProjectRegion (" . $projectRegion->id . ") - updating(): ");
            logger($projectRegion);
        });

        static::updated(function (ProjectRegion $projectRegion) {
            logger("ProjectRegion (" . $projectRegion->id . ") - updated(): ");
            logger($projectRegion);
        });

        static::saved(function (ProjectRegion $projectRegion) {
            logger("ProjectRegion (" . $projectRegion->id . ") - saved(): ");
            logger($projectRegion);
        });

        static::retrieved(function (ProjectRegion $projectRegion) {
            logger("ProjectRegion (" . $projectRegion->id . ") - retrieved(): ");
            logger($projectRegion);
        });

        /************/
    }
}
