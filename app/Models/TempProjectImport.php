<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempProjectImport extends Model implements HasMedia
{
    use CrudTrait, InteractsWithMedia;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tempProjects(): HasMany
    {
        return $this->hasMany(TempProject::class);
    }

    public function validTempProjects(): HasMany
    {
        return $this->hasMany(TempProject::class)->where('valid', 1);
    }

    public function invalidTempProjects(): HasMany
    {
        return $this->hasMany(TempProject::class)->where('valid', 0);
    }

    public function canFinalise(): int
    {
        // if there are temp_projects records imported and there is no invalid temp_projects records
        if ($this->tempProjects->count() > 0 && $this->invalidTempProjects->count() == 0) {
            return 1;
        }

        return 0;
    }

    public function startedAt(): Attribute
    {
        return new Attribute(
            get: fn() =>  $this->created_at->format('Y-m-d'),
        );
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
