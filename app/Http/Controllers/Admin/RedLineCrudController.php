<?php

namespace App\Http\Controllers\Admin;

use App\Models\RedLine;
use App\Imports\RedLineImport;
use App\Http\Requests\RedLineRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Stats4sd\FileUtil\Http\Controllers\Operations\ImportOperation;


class RedLineCrudController extends AdminPanelCrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    // use DeleteOperation { destroy as traitDestroy; }
    use ShowOperation { show as traitShow; }

    use ImportOperation;

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(\App\Models\RedLine::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/red-flag');
        CRUD::setEntityNameStrings('red flag', 'red flags');

        CRUD::set('import.importer', RedLineImport::class);

        parent::setup();



    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', RedLine::class);

        CRUD::setResponsiveTable(false);
        CRUD::column('name')->limit(500);
        CRUD::column('description')->limit(5000);
    }

    protected function setupCreateOperation()
    {
        $this->authorize('create', RedLine::class);

        CRUD::setValidation(RedLineRequest::class);

        CRUD::field('name');
        CRUD::field('description');
    }

    protected function setupUpdateOperation()
    {
        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
    }

    public function destroy($id)
    {
        $this->authorize('delete', RedLine::find($id));

        $this->crud->hasAccessOrFail('delete');

        return $this->crud->delete($id);
    }

    public function show($id)
    {
        $this->authorize('view', RedLine::find($id));

        return $this->traitShow($id);
    }

}
