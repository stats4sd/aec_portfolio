<?php

namespace App\Http\Controllers\Admin;

use App\Models\ScoreTag;
use App\Models\Principle;
use App\Http\Requests\ScoreTagRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use App\Http\Controllers\Admin\Traits\ScoreTagInlineCreateOperation;


class ScoreTagCrudController extends AdminPanelCrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation { destroy as traitDestroy; }

    use ScoreTagInlineCreateOperation;

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(\App\Models\ScoreTag::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/score-tag');
        CRUD::setEntityNameStrings('score tag', 'score tags');

        parent::setup();
    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', ScoreTag::class);

        CRUD::column('principle.name')->label('Principle');
        CRUD::column('name');
        CRUD::column('description');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        // add filter by principle
        $this->crud->addFilter(
            [
                'type' => 'select2',
                'name' => 'principles',
                'label' => 'Filter by Principle',
            ],
            function () {
                return Principle::get()->pluck('name', 'id')->toArray();
            },
            function ($value) {
                $this->crud->addClause('where', 'principle_id', $value);
            }
        );

    }

    protected function setupCreateOperation()
    {
        $this->authorize('create', ScoreTag::class);

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
        $this->authorize('delete', ScoreTag::find($id));

        $this->crud->hasAccessOrFail('delete');

        return $this->crud->delete($id);
    }

}
