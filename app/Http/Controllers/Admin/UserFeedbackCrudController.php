<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserFeedbackRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserFeedbackCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserFeedbackCrudController extends CrudController
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
        CRUD::setModel(\App\Models\UserFeedback::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user-feedback');
        CRUD::setEntityNameStrings('user feedback', 'user feedbacks');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::column('type.name');
        CRUD::column('user.email');
        CRUD::column('created_at');
        CRUD::column('');

        CRUD::enableDetailsRow();
        CRUD::setDetailsRowView('user-feedback.details_row');

        if(!Auth::user()->can('manage user feedback')) {
            CRUD::denyAccess(['list', 'update']);
        }

    }

    protected function setupUpdateOperation()
    {

        CRUD::field('title')
            ->type('section-title')
            ->title('Feedback Details')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');

        CRUD::field('user')->attributes(['disabled' => 'disabled']);


        CRUD::field('userFeedbackType')->attributes(['disabled' => 'disabled']);
        CRUD::field('message')->type('textarea')->attributes(['readonly' => 'readonly']);

        CRUD::field('title')
            ->type('section-title')
            ->content('Add / edit manager comments below')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');

        CRUD::field('comments')
        ->subfields([
            [
                'name' => 'user',
                'type' => 'relationship',
                'default' => Auth::user(),
                'attributes' => ['disabled' => 'disabled'],
            ],
            [
                'name' => 'user_id',
                'type' => 'hidden',
                'default' => Auth::user()->id,
            ],
            [
                'name' => 'message',
                'type' => 'textarea',
                'attributes' => [
                    'readonly' =>
                ]
            ],
        ]);


    }


}
