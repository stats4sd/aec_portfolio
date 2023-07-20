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
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

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

        CRUD::column('user.email');
        CRUD::column('created_at');
        CRUD::column('status');

        CRUD::enableDetailsRow();

        CRUD::setDetailsRowView('user-feedback.details_row');

    }

    protected function setupUpdateOperation()
    {

        CRUD::field('title')
            ->type('section-title')
            ->title('Add Your Feedback')
            ->content('Please enter the details below.<br/><br/>
                    If you are reporting a bug, please tell us what page you were on, what you expected to happen, and what actually happened. If possible, please include a screenshot, as they are very helpful for us to identify the issue.
                   <br/><br/>
                   If you are making a suggestion, please let us know what feature or addition you would like to see. We may get in touch with you for more information. Please tell us if you do <span class="font-weight-bold">Not</span> want us to do that below.
                    ')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');

        CRUD::field('user')->type('hidden')->value(Auth::id());

        CRUD::field('feedbackType')->label('What type of feedback is this?');
        CRUD::field('text')->type('text')->label('Describe the issue:');
        CRUD::field('uploads')->type('upload_multiple')->label('If you have screenshots or supporting files, please upload them here.');



    }


}
