<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdditionalCriteriaScoreTagRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\InlineCreateOperation;

/**
 * Class AdditionalCriteriaScoreTagCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AdditionalCriteriaScoreTagCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use InlineCreateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\AdditionalCriteriaScoreTag::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/additional-criteria-score-tag');
        CRUD::setEntityNameStrings('additional criteria score tag', 'additional criteria score tags');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('additional_criteria_id');
        CRUD::column('name');

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AdditionalCriteriaScoreTagRequest::class);

        CRUD::field('additional_criteria_id');
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
        $this->setupCreateOperation();
    }
}
