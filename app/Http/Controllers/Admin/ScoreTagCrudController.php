<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdditionalCriteriaScoreTag;
use App\Http\Controllers\Admin\Traits\ScoreTagInlineCreateOperation;
use App\Http\Requests\ScoreTagRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ScoreTagCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation { destroy as traitDestroy; }
    use ShowOperation { show as traitShow; }

    use ScoreTagInlineCreateOperation;

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(\App\Models\AdditionalCriteriaScoreTag::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/score-tag');
        CRUD::setEntityNameStrings('score tag', 'score tags');
    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', AdditionalCriteriaScoreTag::class);

        CRUD::column('created_at');
        CRUD::column('description');
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        $this->authorize('create', AdditionalCriteriaScoreTag::class);

        CRUD::setValidation(ScoreTagRequest::class);

        CRUD::field('name');
        CRUD::field('description');
        CRUD::field('principle')->type('relationship');
    }

    protected function setupUpdateOperation()
    {
        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
    }

    public function destroy($id)
    {
        $this->authorize('delete', AdditionalCriteriaScoreTag::find($id));

        $this->crud->hasAccessOrFail('delete');

        return $this->crud->delete($id);
    }

    public function show($id)
    {
        $this->authorize('view', AdditionalCriteriaScoreTag::find($id));

        return $this->traitShow($id);
    }

}
