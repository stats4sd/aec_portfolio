<?php

namespace App\Http\Controllers\Admin;

use App\Models\RedLine;
use App\Imports\RedLineImport;
use App\Http\Requests\RedLineRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Stats4sd\FileUtil\Http\Controllers\Operations\ImportOperation;


/**
 * Class RedLineCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RedLineCrudController extends CrudController
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
        CRUD::setModel(\App\Models\RedLine::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/red-line');
        CRUD::setEntityNameStrings('red line', 'red lines');

        CRUD::set('import.importer', RedLineImport::class);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->authorize('viewAny', RedLine::class);

        CRUD::setResponsiveTable(false);
        CRUD::column('name')->limit(500);
        CRUD::column('description')->limit(5000);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->authorize('create', RedLine::class);

        CRUD::setValidation(RedLineRequest::class);

        CRUD::field('name');
        CRUD::field('description');
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
        $this->authorize('delete', RedLine::find($id));

        $this->crud->hasAccessOrFail('delete');
    
        return $this->crud->delete($id);
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    public function show($id)
    {
        $this->authorize('view', RedLine::find($id));

        $content = $this->traitShow($id);

        return $content;
    }

}
