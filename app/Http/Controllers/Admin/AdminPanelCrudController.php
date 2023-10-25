<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class AdminPanelCrudController extends CrudController
{
    public function setup()
    {
        CRUD::setListView('backpack::crud.admin.list_admin');
        CRUD::setCreateView('backpack::crud.admin.create_admin');
        CRUD::setEditView('backpack::crud.admin.edit_admin');
        CRUD::setReorderView('backpack::crud.admin.reorder_admin');
        CRUD::setShowView('backpack::crud.admin.show_admin');
    }
}
