<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentStatus;
use App\Http\Controllers\Admin\Operations\AssessOperation;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use App\Http\Controllers\Admin\Traits\UsesSaveAndNextAction;
use App\Http\Requests\ProjectRequest;
use App\Imports\ProjectImport;
use App\Models\Principle;
use App\Models\RedLine;
use App\Models\ScoreTag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Illuminate\Support\Str;
use Stats4sd\FileUtil\Http\Controllers\Operations\ImportOperation;

/**
 * Class ProjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProjectCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    use ImportOperation;
    use RedlineOperation;
    use AssessOperation;
    use FetchOperation;
    use UsesSaveAndNextAction;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Project::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project');
        CRUD::setEntityNameStrings('project', 'projects');

        CRUD::set('import.importer', ProjectImport::class);


    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setPersistentTable(false);
        Widget::add()
            ->type('card')
            ->wrapper([
                'class' => 'col-md-10'
            ])
            ->content([
                'body' => 'This page lists all of your projects within the platform. You may add new projects or import from an Excel file. You may also edit existing project details, and perform the 2 types of assessment for each project: <br/><br/>
                    <ol>
                    <li><b>Review Redlines:</b> As the first step, you will screen your project against a list of "red lines". Projects with any red lines do not qualify as agroecological no matter what the rest of the assessment might be. Therefore, any project failing this stage do not go through to the main assessment.</li>
                    <li><b>Assess Project:</b>. You will evaluate the project under each of the 13 principles of Agroecology.</li>
                    </ol>
                    The table below shows all the projects, and the current state of the assessment. Use the buttons in the table below to begin or continue an assessment.
                ',
            ]);

        CRUD::column('number');
        CRUD::column('name');
        CRUD::column('code');
        CRUD::column('budget')->type('number')->prefix('$')->decimals(2)->thousands_sep(',');
        CRUD::column('assessment_status')->type('closure')->function(function ($entry) {
            return $entry->assessment_status?->value;
        });
        CRUD::column('overall_score')->type('number');

        CRUD::filter('assessment_status')
            ->type('select2')
            ->label('Filter by Status')
            ->options(collect(AssessmentStatus::cases())->pluck('value', 'value')->toArray())
            ->active(function ($value) {
                $this->crud->query->where('assessment_status', $value);
            });

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProjectRequest::class);

        CRUD::field('title')->type('section-title')->content('Enter the key project details below. The code should uniquely identify the project within your portfolio');

        CRUD::field('name');
        CRUD::field('code');
        CRUD::field('description');
        CRUD::field('budget');

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

    public function setupAssessOperation()
    {
        Widget::add()->type('script')->content('assets/js/admin/forms/project_assess.js');

         CRUD::field('section-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title(function ($entry) {
                return "Assess Redlines for " . $entry->name;
            })
            ->content('
                    This is the main section of the review. Below are the 13 Agroecology Principles, and you should rate the project against each one. <br/><br/>
                    For each principle, you should give:
                        <ul>
                            <li><b>A rating:</b> This is a number between 0 and 2, based on your appreviation of the value of the principle in the project design / activities, and following the Spectrum defined for each principle. Decimal digits are allowed.</li>
                            <li><b>A comment:</b> Please add any comments to help explain the rating, and about how the principle is seen within the project.</li>
                        </ul>
                    Each principle also lists a set of example activities relevant to that principle. Please tick all activities that are present in the project.<br/><br/>
                    To help track progress, once a rating is given for a principle, that principle name will turn green. Once you have completed and reviewed every principle, please proceed to the final "Confirmation" tab, where you can mark the assessment as complete. Once done, you will return to the main projects list to view the final result.
          ');


        CRUD::enableVerticalTabs();
        // cannot use relationship with repeatable because we need to filter the scoretags...
        $entry = CRUD::getCurrentEntry();

        foreach ($entry->principles as $principle) {

            CRUD::field($principle->id . '_title')
                ->tab($principle->name)
                ->type('section-title')
                ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
                ->title($principle->name);

            if ($principle->can_be_na) {
                CRUD::field($principle->id . "_is_na")
                    ->tab($principle->name)
                    ->attributes([
                        'data-to-disable' => $principle->id,
                    ])
                    ->type('boolean')
                    ->label('If this principle is not applicable for this project, tick this box.')
                    ->default($principle->pivot->is_na);

            }


            CRUD::field($principle->id . '_rating')
                ->tab($principle->name)
                ->label('Rating for ' . $principle->name)
                ->attributes([
                    'data-tab' => Str::slug($principle->name),
                    'data-update-tab' => '1',
                ])
                ->min(0)
                ->max(2)
                ->default($principle->pivot->rating);



            CRUD::field($principle->id . '_rating_comment')
                ->tab($principle->name)
                ->label('Comment for ' . $principle->name)
                ->type('textarea')
                ->default($principle->pivot->rating_comment);

            CRUD::field($principle->id . '_scoreTags')
                ->tab($principle->name)
                ->label('Presence of Examples/Indicators for ' . $principle->name)
                ->type('checklist')
                ->number_of_columns(1)
                ->model(ScoreTag::class)
                ->options(function ($query) use ($principle) {
                    return $query->where('principle_id', $principle->id)
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->default($principle->principleProjects()->where('project_id', $entry->id)->first()?->scoreTags->pluck('id')->toArray() ?? []);
        }

        CRUD::field('complete_title')
            ->type('section-title')
            ->tab('Confirm Assessment')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->content('
                Once you have completed the review of each principle, and are satisfied that the above entries are correct, please tick this box to confirm the review.<br/>
                <i>(Note: You may still edit this review after marking it as complete)</i>
            ');

        CRUD::field('assessment_incomplete_note')
            ->type('section-title')
            ->tab('Confirm Assessment')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->content('You have not given a rating for every principle. You must complete each principle before marking the assessment as complete.')
            ->variant('warning');

        CRUD::field('assessment_complete')
            ->type('boolean')
            ->tab('Confirm Assessment')
            ->label('I confirm the assessment is complete')
            ->attributes([
                'data-check-complete' => '1',
            ])
        ->default($entry->assessment_status === AssessmentStatus::Complete);


        CRUD::field('assessment_incomplete')
            ->type('boolean')
            ->tab('Confirm Assessment')
            ->label('I confirm the assessment is complete')
            ->attributes([
                'disabled' => 'disabled',
                'readonly' => 'readonly',
            ]);

        $this->setupCustomSaveActions('assess');


    }

    public function setupRedlineOperation()
    {

         Widget::add()->type('script')->content('assets/js/admin/forms/redlines.js');


        CRUD::setHeading('');
        $entry = CRUD::getCurrentEntry();

        CRUD::field('section-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title(function ($entry) {
                return "Assess Redlines for " . $entry->name;
            })
            ->content('
                    Listed below is the set of red lines to check for each project.<br/><br/>
                    These are the Red Line elements, which are counter-productive or harmful to the values and principles of agroecology. If any one of these is present in the project being rated, then the Agroecology Overall Score is 0.<br/><br/>
                    If any one of these is not relevant for the assessed project, select "N/A"
                          ');

        // We cannot use the relationship with subfields field here, because we do not want the user to be able to unassign any redlines from the project.
        foreach ($entry->redLines as $redline) {
            CRUD::field('redline_title_' . $redline->id)
                ->wrapper([
                    'class' => 'col-md-6'
                ])
                ->type('custom_html')
                ->value("<h4>{$redline->name}</h4><p>{$redline->description}</p>");

            CRUD::field('redline_value_' . $redline->id)
                ->label('Present?')
                ->default($redline->pivot->value)
                ->type('select_from_array')
                ->attributes([
                    'data-required' => '1',
                ])
                ->wrapper([
                    'class' => 'col-md-6'
                ])
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                    -99 => 'N/A',
                ]);

            CRUD::field('redline_divider_' . $redline->id)
                ->type('custom_html')
                ->value('<hr/>');

        }

        CRUD::field('complete_title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->content('
                Once you have completed the review of each redline, and are satisfied that the above entries are correct, please tick this box to confirm the review.<br/>
                <i>(Note: You may still edit this review after marking it as complete)</i>
                ');


        CRUD::field('redlines_complete')
            ->type('boolean')
            ->label('I confirm the Redlines assessment is complete');

        CRUD::field('redlines_incomplete')
            ->type('boolean')
            ->label('I confirm the Redlines assessment is complete')
            ->hint('You must assign a value (or mark as NA) for every redline above before marking the redline assessment as complete.')
        ->attributes([
            'disabled' => 'disabled',
    ]);

    }


    public function fetchScoreTag()
    {
        $test = collect(request()->input('form'));
    }

}