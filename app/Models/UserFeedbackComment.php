<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFeedbackComment extends Model
{
    use CrudTrait;

    protected $guarded = [];
    public function userFeedback(): BelongsTo
    {
        return $this->belongsTo(UserFeedback::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);

    }

}
