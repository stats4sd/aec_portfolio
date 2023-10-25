<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundingSource extends Model
{

    use CrudTrait;

    protected $guarded = [];
    protected $table = 'funding_sources';

    public static function booted()
    {

        static::saving(function (FundingSource $fundingSource) {
            if($fundingSource->institution_id) {
                $fundingSource->source = Organisation::withoutGlobalScopes()->find($fundingSource->institution_id)->name;
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // which organisation is the source of these funds?
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

}
