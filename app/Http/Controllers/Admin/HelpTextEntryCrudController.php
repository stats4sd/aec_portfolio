<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\HelpTextItemRequest;
use App\Models\HelpTextEntry;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class HelpTextItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HelpTextEntryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\HelpTextEntry::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/help-text-entry');
        CRUD::setEntityNameStrings('help text entries', 'help text entries');

        if(auth()->user()->cannot('manage help text entries')) {
            CRUD::denyAccess(['list', 'update']);
        }

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        Widget::add()
            ->to('before_content')
            ->type('card')
            ->wrapper('col-10')
            ->content([
                'body' => 'This page allows you to review and update the helper text available throughout the platform. All the text available via the ? symbols can be updated below. Only the text can be changed - there is no way to add additional help text entries through this system.'
            ]);

        CRUD::column('name');
        CRUD::column('text');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::field('info')
            ->type('section-title')
            ->title('Edit Help Text')
            ->content('Use the form below to edit the help text. The location below is the page and position that this text will appear to users of the platform')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');

        CRUD::field('location')->attributes(['disabled' => 'disabled', 'readonly' => 'readonly']);
        CRUD::field('text')->type('summernote');
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


    // helper class to return the correct help text for use in a vue component.
    public function find(string $location)
    {
        return HelpTextEntry::firstWhere('location', $location);
    }
}
