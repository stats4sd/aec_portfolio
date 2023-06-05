<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Imports\PrincipleImport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScoreTag extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    public function principleAssessment(): BelongsToMany
    {
        return $this->belongsToMany(PrincipleAssessment::class);
    }

    public function principle(): BelongsTo
    {
        return $this->belongsTo(Principle::class);
    }
}
