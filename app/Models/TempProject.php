<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class TempProject extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    protected $casts = [
        'valid' => 'boolean',
    ];

    protected static function booted()
    {
        // add global scope to show temp_projects belong to logged in user
        static::addGlobalScope('user', function (Builder $builder) {
            $user = auth()->user();

            $tempProjectImportId = 0;

            // get selectedOrganisationId from session
            $selectedOrganisationId = Session::get('selectedOrganisationId');

            // find TempProjectImport model for this user and this organisation
            foreach ($user->tempProjectImports as $tempProjectImport) {
                if ($tempProjectImport->organisation_id == $selectedOrganisationId) {
                    $tempProjectImportId = $tempProjectImport->id;
                }
            }

            $builder->where('temp_project_import_id', $tempProjectImportId);
        });
    }

    public function tempProjectImport(): BelongsTo
    {
        return $this->belongsTo(TempProjectImport::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class)
            ->withoutGlobalScope('organisation');
    }
}
