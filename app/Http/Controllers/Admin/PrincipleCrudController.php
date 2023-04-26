<?php

namespace App\Http\Controllers\Admin;

use App\Models\Principle;
use App\Http\Requests\PrincipleRequest;
use App\Imports\PrincipleImport;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Stats4sd\FileUtil\Http\Controllers\Operations\ImportOperation;

/**
 * Class PrincipleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PrincipleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }

    use ImportOperation;

    use AuthorizesRequests;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Principle::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/principle');
        CRUD::setEntityNameStrings('principle', 'principles');

        CRUD::set('import.importer', PrincipleImport::class);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->authorize('viewAny', Principle::class);

        CRUD::column('name');
        CRUD::column('can_be_na');

        CRUD::enableDetailsRow();
        CRUD::setDetailsRowView('details.principle');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
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

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
    }

    /**
     * Define what happens when the Delete operation is loaded.
     */
    public function destroy($id)
    {
        $this->authorize('delete', Principle::find($id));

        $this->crud->hasAccessOrFail('delete');
    
        return $this->crud->delete($id);
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    public function show($id)
    {
        $this->authorize('view', Principle::find($id));

        $content = $this->traitShow($id);

        return $content;
    }

}
