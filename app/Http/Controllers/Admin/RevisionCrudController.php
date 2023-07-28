<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RevisionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RevisionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RevisionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Revision::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/revision');
        CRUD::setEntityNameStrings('revision', 'revisions');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('revisionable_type');
        CRUD::column('revisionable_id');

        $this->crud->addColumns([
            [
                'name' => 'user_id',
                'label' => 'User',
                'type' => 'select',
                'entity' => 'user',
                'attribute' => 'name',
                'model' => User::class,
                'wrapper' => [
                    'element' => 'div',
                    'width' => "200px",
                ],
            ],
        ]);

        // CRUD::column('user_id');

        CRUD::column('key');
        CRUD::column('old_value');
        CRUD::column('new_value');
        CRUD::column('created_at');
        // CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    // /**
    //  * Define what happens when the Create operation is loaded.
    //  * 
    //  * @see https://backpackforlaravel.com/docs/crud-operation-create
    //  * @return void
    //  */
    // protected function setupCreateOperation()
    // {
    //     CRUD::setValidation(RevisionRequest::class);

    //     CRUD::field('revisionable_type');
    //     CRUD::field('revisionable_id');
    //     CRUD::field('user_id');
    //     CRUD::field('key');
    //     CRUD::field('old_value');
    //     CRUD::field('new_value');

    //     /**
    //      * Fields can be defined using the fluent syntax or array syntax:
    //      * - CRUD::field('price')->type('number');
    //      * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
    //      */
    // }

    // /**
    //  * Define what happens when the Update operation is loaded.
    //  * 
    //  * @see https://backpackforlaravel.com/docs/crud-operation-update
    //  * @return void
    //  */
    // protected function setupUpdateOperation()
    // {
    //     $this->setupCreateOperation();
    // }
}
