<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Imports\PrincipleImport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomScoreTag extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    public function principleAssessment(): BelongsTo
    {
        return $this->belongsTo(PrincipleAssessment::class);
    }

    public function principle(): BelongsTo
    {
        return $this->belongsTo(Principle::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
