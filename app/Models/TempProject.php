<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempProject extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    public function tempProjectImport(): BelongsTo
    {
        return $this->belongsTo(TempProjectImport::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class)
            ->withoutGlobalScope('organisation');
    }
}
