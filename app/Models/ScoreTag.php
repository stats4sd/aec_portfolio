<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Imports\PrincipleImport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreTag extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    public function principleProject()
    {
        return $this->belongsToMany(PrincipleProject::class);
    }

    public function principle()
    {
        return $this->belongsTo(Principle::class);
    }
}
