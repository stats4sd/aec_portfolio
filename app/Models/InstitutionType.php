<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstitutionType extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'institution_types';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('ordering', function (Builder $builder) {
             $builder->orderBy('lft');
        });
    }

    public function organisations(): HasMany
    {
        return $this->hasMany(Organisation::class);
    }
}
