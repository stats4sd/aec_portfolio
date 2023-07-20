<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserFeedback extends Model implements HasMedia
{
    use CrudTrait, InteractsWithMedia;

    protected $table = 'user_feedbacks';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('owned', function (Builder $builder) {

            // if the current user can maintain users, they can see all feedback.
            if(Auth::user()->can('manage user feedback')) {
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(UserFeedbackType::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(UserFeedbackComment::class);
    }
}
