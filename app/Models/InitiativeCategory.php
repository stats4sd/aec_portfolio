<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Pest\Plugins\Init;

class InitiativeCategory extends Model
{
    use CrudTrait;


    protected $table = 'initiative_categories';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('ordering', function (Builder $builder) {
            $builder->orderBy('lft');
        });
    }


    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }


}
