<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Imports\PrincipleImport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Matrix\Operators\Addition;

class AdditionalCriteriaScoreTag extends Model
{
    use CrudTrait, SoftDeletes;

    protected $guarded = ['id'];

    public function additionalCriteriaAssessment(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalCriteriaAssessment::class)
            ->withPivot('assessment_id');
    }

    public function additionalCriteria(): BelongsTo
    {
        return $this->belongsTo(AdditionalCriteria::class);
    }

}
