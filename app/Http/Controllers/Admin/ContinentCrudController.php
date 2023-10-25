<?php

namespace App\Http\Controllers\Admin;

use App\Models\Continent;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ContinentCrudController extends AdminPanelCrudController
{
    use ListOperation;

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(Continent::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/continent');
        CRUD::setEntityNameStrings('continent', 'continents');

        parent::setup();
    }

    protected function setupListOperation()
    {
        $this->authorize('viewAny', Continent::class);

        CRUD::column('id')->label('Continent ID');
        CRUD::column('name');
    }

}
