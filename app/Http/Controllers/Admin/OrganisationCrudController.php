<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organisation;
use App\Http\Requests\OrganisationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class OrganisationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrganisationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }

    use AuthorizesRequests;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        logger("OrganisationCrudController.setup()...");

        CRUD::setModel(\App\Models\Organisation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/organisation');
        CRUD::setEntityNameStrings('institution', 'institutions');

        CRUD::denyAccess('delete');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        logger("OrganisationCrudController.setupListOperation()...");

        $this->authorize('viewAny', Organisation::class);

        CRUD::setResponsiveTable(false);
        CRUD::column('name');
        CRUD::column('projects')->type('relationship_count');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        logger("OrganisationCrudController.setupCreateOperation()...");

        $this->authorize('create', Organisation::class);

        CRUD::field('name')->label('Enter the Institution name');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        logger("OrganisationCrudController.setupUpdateOperation()...");

        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
    }

    /**
     * Define what happens when the Delete operation is loaded.
     */
    public function destroy($id)
    {
        $this->authorize('delete', Organisation::find($id));

        $this->crud->hasAccessOrFail('delete');
    
        return $this->crud->delete($id);
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    public function show($id)
    {
        $organisation = Organisation::find($id);

        $this->authorize('view', $organisation);

        return view('organisations.show', ['organisation' => $organisation]);
    }

}
