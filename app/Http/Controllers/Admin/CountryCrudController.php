<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CountryCrudController extends CrudController
{
    use ListOperation;

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(Country::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/country');
        CRUD::setEntityNameStrings('country', 'countries');
    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', Country::class);

        CRUD::column('continent.id')->label('Continent ID');
        CRUD::column('continent.name')->label('Continent');
        CRUD::column('region.id')->label('Region ID');
        CRUD::column('region')->type('relationship')->attribute('name')->key('region');
        CRUD::column('id')->label('Country ID');
        CRUD::column('name');
    }

}
