<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        static::addGlobalScope('organisation',  function (Builder $query) {
            $query->where('organisation_id', Session::get('selectedOrganisationId'));
        });

        // attach event handler, on deleting of a portfolio
        static::deleting(function ($portfolio) {
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

    public function tempProjects()
    {
        return $this->hasMany(TempProject::class);
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

    public function getFundingFlowAnalysisAttribute()
    {
        return $this->contributes_to_funding_flow == 1 ? 'Yes' : 'No';
    }

    // computed field to show budget with thousand separator
    public function displayBudget(): Attribute
    {
        return new Attribute(
            get: fn() => number_format($this->budget),
        );
    }
}
