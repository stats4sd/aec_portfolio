<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portfolio extends Model
{
    use CrudTrait, HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'portfolios';
    protected $guarded = ['id'];

    protected static function booted()
    {
        // add global scope to filter by currently selected organisation.
        static::addGlobalScope('organisation',  function(Builder $query) {
            $query->where('organisation_id', Session::get('selectedOrganisationId'));
        });

        /************/

        static::updating(function (Portfolio $portfolio) {
            logger("Portfolio (" . $portfolio->id . ") - updating()");
        });

        static::updated(function (Portfolio $portfolio) {
            logger("Portfolio (" . $portfolio->id . ") - updated()");
        });

        static::saved(function (Portfolio $portfolio) {
            logger("Portfolio (" . $portfolio->id . ") - saved()");
        });

        static::retrieved(function (Portfolio $portfolio) {
            logger("Portfolio (" . $portfolio->id . ") - retrieved()");
        });

        /************/

        // attach event handler, on deleting of a portfolio
	    static::deleting(function($portfolio) {
            // delete all projects that belong to this portfolio
            foreach ($portfolio->projects as $project) {
                $project->delete();
            }
	    });

    }


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
