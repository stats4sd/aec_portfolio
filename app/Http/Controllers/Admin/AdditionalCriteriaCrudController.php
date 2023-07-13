<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AssessmentCriteriaRequest;
use App\Models\AdditionalCriteria;
use App\Models\AdditionalCriteriaScoreTag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Backpack\Pro\Http\Controllers\Operations\InlineCreateOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use Prologue\Alerts\Facades\Alert;

class AdditionalCriteriaCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    use AuthorizesRequests;
    use InlineCreateOperation;
    use ReorderOperation;
    use FetchOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\AdditionalCriteria::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/additional-criteria');
        CRUD::setEntityNameStrings('additional criteria', 'additional criteria');
    }

    protected function setupListOperation()
    {
        // $this->authorize('viewAny', AssessmentCriteria::class);

        CRUD::setPersistentTable(false);
        CRUD::setResponsiveTable(false);
        //CRUD::enableDetailsRow();
        CRUD::setDetailsRowView('details.additional_criteria');

        $this->crud->addClause('where', 'organisation_id', Session::get('selectedOrganisationId'));


        Widget::add()
            ->type('card')
            ->wrapper([
                'class' => 'col-md-10'
            ])
            ->content([
                'body' => 'By default, all projects or initiatives from <b>every</b> institution are reviewed against the 13 Principles of Agroecology to get a harmonised approximation of how agro-ecological they are, and enable effective comparison of results. <br/><br/>
                    Your institution may also wish to assess using additional criteria. For example, you might be especially interested in participatory / co-creation aspects of work that your institution funds, so you could add a "co-creation" item here, with information about how your assessors should review projects. These additional criteria will be presented as an extra step in the assessment process, and the resulting data will be available to your institution to download.
                ',
            ]);

        CRUD::column('name');
        CRUD::column('can_be_na');
    }

    public function setupShowOperation()
    {
        CRUD::column('name');

        CRUD::column('description')->type('textarea');

        CRUD::column('link');

        CRUD::column('Rating Definitions');
        CRUD::column('rating_two')->label('Score of 2.0:');
        CRUD::column('rating_one')->label('Score of 1.0:');
        CRUD::column('rating_zero')->label('Score of 0.0:');
        CRUD::column('can_be_na')->type('boolean');


    }

    protected function setupCreateOperation()
    {

        //   $this->authorize('create', AssessmentCriteria::class);

        CRUD::setValidation(AssessmentCriteriaRequest::class);

        CRUD::field('organisation_id')->type('hidden')->value(Session::get('selectedOrganisationId'));
        CRUD::field('name')
            ->label('Enter the name for the new Assessment Criterium');
        CRUD::field('description')
            ->label('Add a brief description. This will be presented to users on the assessment page.');
        CRUD::field('link')
            ->label('If there is more information about this item somewhere online, enter the link here.')
            ->hint('This could be a link to your own institutional website, or to external information if this assessment criteria is used by multiple institutions.');

        CRUD::field('rating_description_header')
            ->type('section-title')
            ->title('What do the ratings mean?')
            ->content('Each project or initiative will be given a score of 0 - 2 for this assessment criterium. Below, please enter a brief description of what a rating of 0, 1 or 2 means. This will help harmonise the rating process and let different users give scores that are comparable.')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');
        CRUD::field('rating_two')->label('What does a rating of "2" mean?');
        CRUD::field('rating_one')->label('What does a rating of "1" mean?');
        CRUD::field('rating_zero')->label('What does a rating of "0" mean?');
        CRUD::field('can_be_na')->label('Can this assessment criterium be marked as "na"?')
            ->hint('If this is no, then every project or initiative must be given a rating for this.');
        CRUD::field('rating_na')->label('What does a rating of "na" mean?');

        CRUD::field('score-tag-title')
            ->type('section-title')
            ->title('Add Examples / Indicators')
            ->content('Optionally, you may add any number of examples or indicators that an initiative may use to show evidence of this criteria. These items will appear within each assessment, and users may tick items that are present or demonstrable within the initiative.')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');


        CRUD::field('additionalCriteriaScoreTags')
            ->label('Add Examples / Indicators')
            ->type('relationship')
            ->subfields([
                [
                    'name' => 'name',
                    'type' => 'text',
                ],
                [
                    'name' => 'description',
                    'type' => 'textarea',
                ],
            ]);

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'name');
        CRUD::set('reorder.max_level', 0);

        Widget::add()
            ->type('card')
            ->wrapper([
                'class' => 'col-md-10'
            ])
            ->content([
                'body' => 'Drag and drop the items below to re-order them. This is the order they will appear to users during the assessment process.
                ',
            ]);
    }

    public function fetchAdditionalCriteriaScoreTags()
    {
        return $this->fetch([
            'model' => AdditionalCriteriaScoreTag::class,
            'query' => function (AdditionalCriteriaScoreTag $model) {
                return $model->whereHas('additionalCriteria', function (Builder $query) {
                    $query->where('organisation_id', '=', Session::get('selectedOrganisationId'));
                });
            },
        ]);
    }

    public function destroy($id)
    {
        $criterium = AdditionalCriteria::find($id);
        $this->authorize('update', $criterium->institution);

        $criterium->delete();

        Alert::add('success', "$criterium->name was successfully deleted")->flash();

        return back();
    }

}
