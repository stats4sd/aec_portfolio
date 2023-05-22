<?php

namespace App\Http\Controllers\Admin;

use App\Models\ScoreTag;
use App\Http\Controllers\Admin\Traits\ScoreTagInlineCreateOperation;
use App\Http\Requests\ScoreTagRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Backpack\Pro\Http\Controllers\Operations\InlineCreateOperation;

/**
 * Class ScoreTagCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ScoreTagCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }

    use ScoreTagInlineCreateOperation;

    use AuthorizesRequests;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ScoreTag::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/score-tag');
        CRUD::setEntityNameStrings('score tag', 'score tags');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->authorize('viewAny', ScoreTag::class);

        CRUD::column('created_at');
        CRUD::column('description');
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('updated_at');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->authorize('create', ScoreTag::class);

        CRUD::setValidation(ScoreTagRequest::class);

        CRUD::field('name');
        CRUD::field('description');
        CRUD::field('principle')->type('relationship');
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
        $this->authorize('delete', ScoreTag::find($id));

        $this->crud->hasAccessOrFail('delete');
    
        return $this->crud->delete($id);
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    public function show($id)
    {
        $this->authorize('view', ScoreTag::find($id));

        $content = $this->traitShow($id);

        return $content;
    }

}
