<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RedLine extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        parent::booted();

        static::created(function($redLine): void {
            $redLine->projects()->sync(Project::all()->pluck('id')->toArray());
        });

    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot([
                'value'
            ]);
    }

    public function failingProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->wherePivot('value', 1);
    }

    public function assessmentRedLines(): HasMany
    {
        return  $this->hasMany(AssessmentRedLine::class);
    }
}
