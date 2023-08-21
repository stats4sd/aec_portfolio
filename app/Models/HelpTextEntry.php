<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpTextEntry extends Model
{
    use CrudTrait;

    protected $table = 'help_text_entries';
    protected $guarded = ['id'];

}
