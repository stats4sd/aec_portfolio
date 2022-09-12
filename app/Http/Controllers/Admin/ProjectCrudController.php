<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\AssessOperation;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use App\Http\Requests\ProjectRequest;
use App\Imports\ProjectImport;
use App\Models\Principle;
use App\Models\RedLine;
use App\Models\ScoreTag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
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
        CRUD::column('number');
        CRUD::column('name');
        CRUD::column('code');
        CRUD::column('description');
        CRUD::column('budget');

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

        CRUD::field('name');
        CRUD::field('code');
        CRUD::field('description');
        CRUD::field('budget');

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

    public function setupAssessOperation()
    {
        //Widget::add()->type('script')->content('assets/js/admin/forms/project_assess.js');

        // cannot use relationship with repeatable because we need to filter the scoretags...
        $entry = CRUD::getCurrentEntry();

        foreach ($entry->principles as $principle) {

            CRUD::field($principle->id . '_title')
                ->tab($principle->name)
                ->type('section-title')
                ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
                ->title($principle->name);

            CRUD::field($principle->id . '_rating')
                ->tab($principle->name)
                ->label('Rating for ' . $principle->name)
                ->type('number')
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
            CRUD::field($principle->id . 'divider')
                ->type('custom_html')
                ->content('</hr>');
        }
    }

    public function setupRedlineOperation()
    {
        CRUD::setHeading('');


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


        CRUD::field('redLines')
            ->type('relationship')
            ->subFields([
                // hidden field to replace the pivotselect
                [
                    'name' => 'redlines',
                    'type' => 'hidden',
                    'value' => ,
                ],
                [
                    'name' => 'value',
                    'type' => 'select_from_array',
                    'options' => [
                        1 => 'Yes',
                        0 => 'No',
                        -99 => 'N/A',
                    ],
                    'wrapper' => [
                        'class' => 'col-md-6',
                    ],
                ],
            ])
            ->pivotSelect([
                'label' => 'Name',
                'attributes' => ['disabled' => 'disabled'],
                'wrapper' => [
                    'class' => 'col-md-6',
                ]
            ])
            ->min_rows(RedLine::all()->count())
            ->max_rows(RedLine::all()->count())
            ->tab('Red Lines');
    }


    public function fetchScoreTag()
    {
        $test = collect(request()->input('form'));
    }

}
