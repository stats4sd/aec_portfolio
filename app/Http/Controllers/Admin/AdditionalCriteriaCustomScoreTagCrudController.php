<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdditionalCriteriaCustomScoreTagRequest;
use App\Models\AdditionalCriteriaCustomScoreTag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


class AdditionalCriteriaCustomScoreTagCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(AdditionalCriteriaCustomScoreTag::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/additional-criteria-custom-score-tag');
        CRUD::setEntityNameStrings('additional criteria custom score tag', 'additional criteria custom score tags');
    }

    protected function setupListOperation()
    {
    }
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AdditionalCriteriaCustomScoreTagRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
