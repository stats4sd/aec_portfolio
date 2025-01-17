<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempProject extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    public function projectImport(): BelongsTo
    {
        return $this->belongsTo(ProjectImport::class);
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
