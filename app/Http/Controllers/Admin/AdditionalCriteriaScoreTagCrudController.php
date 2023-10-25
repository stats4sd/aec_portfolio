<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdditionalCriteriaScoreTagRequest;
use App\Models\AdditionalCriteriaScoreTag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\InlineCreateOperation;

class AdditionalCriteriaScoreTagCrudController extends CrudController
{
    use CreateOperation;
    use InlineCreateOperation;

    public function setup()
    {
        CRUD::setModel(AdditionalCriteriaScoreTag::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/additional-criteria-score-tag');
        CRUD::setEntityNameStrings('additional criteria score tag', 'additional criteria score tags');
    }

    protected function setupListOperation()
    {
        CRUD::column('additional_criteria_id');
        CRUD::column('name');

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(AdditionalCriteriaScoreTagRequest::class);

        CRUD::field('additional_criteria_id');
        CRUD::field('name');
        CRUD::field('description');

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
