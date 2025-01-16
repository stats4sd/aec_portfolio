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
}
