<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use CrudTrait;

    protected $table = 'system_logs';
    protected $guarded = ['id'];
}
