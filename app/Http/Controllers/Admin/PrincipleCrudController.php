<?php

namespace App\Http\Controllers\Admin;

use App\Models\Principle;
use App\Http\Requests\PrincipleRequest;
use App\Imports\PrincipleImport;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Stats4sd\FileUtil\Http\Controllers\Operations\ImportOperation;

class PrincipleCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation { destroy as traitDestroy; }
    use ShowOperation { show as traitShow; }

    use ImportOperation;

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(Principle::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/principle');
        CRUD::setEntityNameStrings('principle', 'principles');

        CRUD::set('import.importer', PrincipleImport::class);
    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', Principle::class);

        CRUD::column('name');
        CRUD::column('can_be_na');

        CRUD::enableDetailsRow();
        CRUD::setDetailsRowView('details.principle');
    }

    protected function setupCreateOperation()
    {
        $this->authorize('create', Principle::class);

        CRUD::setValidation(PrincipleRequest::class);

        CRUD::field('name');
        CRUD::field('rating_two');
        CRUD::field('rating_one');
        CRUD::field('rating_zero');
        CRUD::field('rating_na');
        CRUD::field('can_be_na');
    }

    protected function setupUpdateOperation()
    {
        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
    }

    public function destroy($id)
    {
        $this->authorize('delete', Principle::find($id));

        $this->crud->hasAccessOrFail('delete');

        return $this->crud->delete($id);
    }

    public function show($id)
    {
        $this->authorize('view', Principle::find($id));

        return $this->traitShow($id);
    }

}
