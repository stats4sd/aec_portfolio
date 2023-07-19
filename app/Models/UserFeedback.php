<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class UserFeedback extends Model
{
    use CrudTrait;

    protected $table = 'user_feedbacks';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('owned', function (Builder $builder) {

            // if the current user can maintain users, they can see all feedback.
            if(Auth::user()->can('maintain users')) {
                return;
            }

            // otherwise, they see their own feedback.
            $builder->where('user_id', Auth::id());

        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackType(): BelongsTo
    {
        return $this->belongsTo(UserFeedbackType::class);
    }
}
