<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class RegionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RegionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        // TODO: to be revised with a comprehensive roles and permissions configuration
        if ( !auth()->user()->hasRole('admin') ) {
            throw new AccessDeniedHttpException('This page is only available to site admin');
        }

        CRUD::setModel(\App\Models\Region::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/region');
        CRUD::setEntityNameStrings('region', 'regions');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::column('continent_id');
        CRUD::column('continent.id')->label('Continent ID');
        CRUD::column('continent')->type('relationship')->attribute('name');
        CRUD::column('id')->label('Region ID');
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
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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
