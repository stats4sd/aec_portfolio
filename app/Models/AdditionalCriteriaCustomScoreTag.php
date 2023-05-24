<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalCriteriaCustomScoreTag extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'additional_criteria_custom_score_tags';
    protected $guarded = ['id'];

    public function additionalCriteriaAssessment():  BelongsTo
    {
        return $this->belongsTo(AdditionalCriteriaAssessment::class);
    }

    public function additionalCriteria(): BelongsTo
    {
        return $this->belongsTo(AdditionalCriteria::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }


}
