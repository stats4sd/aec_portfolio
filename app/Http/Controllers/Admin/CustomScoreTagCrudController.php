<?php

namespace App\Http\Controllers\Admin;

use App\Models\Principle;
use App\Http\Requests\CustomScoreTagRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CustomScoreTagCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CustomScoreTagCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\CustomScoreTag::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/custom-score-tag');
        CRUD::setEntityNameStrings('custom score tag', 'custom score tags');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('principleassessment.principle.name')->label('Principle');
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
                $this->crud->query->whereHas('principleAssessment', function ($q) use ($value) {
                    $q->where('principle_id', '=', $value);
                });
            }
        );
        
    }

}
