<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RegionCrudController extends CrudController
{
    use ListOperation;

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(\App\Models\Region::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/region');
        CRUD::setEntityNameStrings('region', 'regions');
    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', Region::class);

        CRUD::column('continent.id')->label('Continent ID');
        CRUD::column('continent')->type('relationship')->attribute('name');
        CRUD::column('id')->label('Region ID');
        CRUD::column('name');

    }

}
