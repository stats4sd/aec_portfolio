<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserFeedbackType extends Model
{
    use CrudTrait;

    protected $table = 'user_feedback_types';
    protected $guarded = ['id'];


    public function userFeedbacks(): HasMany
    {
        return $this->hasMany(UserFeedback::class, 'user_feedback_type_id');
    }
}
