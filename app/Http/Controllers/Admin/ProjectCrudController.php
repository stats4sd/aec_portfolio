<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use App\Models\Country;
use App\Models\Project;
use App\Models\RedLine;
use App\Models\AdditionalCriteriaScoreTag;
use App\Models\Portfolio;
use App\Models\Principle;
use App\Models\Assessment;
use Illuminate\Support\Str;
use App\Models\Organisation;
use App\Imports\ProjectImport;
use App\Models\CustomScoreTag;
use App\Enums\AssessmentStatus;
use App\Enums\GeographicalReach;
use App\Models\OrganisationMember;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProjectRequest;
use Backpack\CRUD\app\Library\Widget;
use function mysql_xdevapi\getSession;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\True_;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Admin\Operations\AssessOperation;
use App\Http\Controllers\Admin\Operations\ImportOperation;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use App\Http\Controllers\Admin\Traits\UsesSaveAndNextAction;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    use AuthorizesRequests;

    use ImportOperation;
    use AssessOperation;
    use RedlineOperation;
    use FetchOperation;
    use UsesSaveAndNextAction;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        if ( !Session::exists('selectedOrganisationId') ) {
            throw new BadRequestHttpException('Please select an institution first');
        }

        CRUD::setModel(\App\Models\Project::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project');
        CRUD::setEntityNameStrings('initiative', 'initiatives');

        CRUD::set('import.importer', ProjectImport::class);
        CRUD::set('import.template-path', 'AE Marker - Project Import Template.xlsx');

        CRUD::setShowView('projects.show');

    }

    public function show($id)
    {
        $this->authorize('view', Project::find($id));

        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry (include softDeleted items if the trait is used)
        if ($this->crud->get('show.softDeletes') && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->crud->model))) {
            $this->data['entry'] = $this->crud->getModel()->withTrashed()->findOrFail($id);
        } else {
            $this->data['entry'] = $this->crud->getEntryWithLocale($id);
        }

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview') . ' ' . $this->crud->entity_name;


        // #### ADD SPIDER CHART DATA ###
        $this->data['spiderData'] = $this->data['entry']->assessments->last()->principleProjects->map(function ($principleProject) {
            return [
                'axis' => $principleProject->principle->name,
                'value' => $principleProject->rating,
            ];
        });


        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getShowView(), $this->data);
    }


    public function showAssessment($id)
    {
        $assessment = Assessment::find($id);

        $project = $assessment->project;

        $this->authorize('view', $project);

        $this->crud->hasAccessOrFail('show');

        $this->data['entry'] = $project;
        $this->data['assessment'] = $assessment;

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview') . ' ' . $this->crud->entity_name;


        // #### ADD SPIDER CHART DATA ###
        $this->data['spiderData'] = $assessment->principleAssessments->map(function ($principleAssessment) {
            return [
                'axis' => $principleAssessment->principle->name,
                'value' => $principleAssessment->rating,
            ];
        });

        return view('assessments.show', $this->data);
    }


    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->authorize('viewAny', Project::class);

        // remove the default Preview button for project
        $this->crud->removeButton('show');

        // add custom Preview button for assessment
        $this->crud->addButtonFromView('line', 'preview_latest_assessment', 'preview_latest_assessment', 'start');
        $this->crud->addButton('line', 'assess_custom', 'view', 'crud::buttons.assess_custom')->makeFirst();
        $this->crud->addButton('line', 'assess', 'view', 'crud::buttons.assess')->makeFirst();
        $this->crud->addButton('line', 'redline', 'view', 'crud::buttons.redline')->makeFirst();

        // add Re-Assess Project button
        // Question: Um... how to add this button next to Assess Project button...?
        $this->crud->addButtonFromView('line', 're-assess', 're-assess', 'end');

        CRUD::setPersistentTable(false);
        CRUD::setResponsiveTable(false);

        CRUD::enableDetailsRow();
        CRUD::setDetailsRowView('details.project');

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

        CRUD::column('organisation')->type('relationship')->label('Institution');
        CRUD::column('portfolio')->type('relationship')->label('Portfolio');
        CRUD::column('name');
        CRUD::column('code');
        CRUD::column('budget')->type('closure')->function(function ($entry) {
            $value = number_format($entry->budget, 2, '.', ',');
            return "{$entry->currency} {$value}";
        });

        CRUD::column('latest_assessment_status')->label('Assessment status');

        CRUD::column('score')->type('closure')->label('Overall score')->function(function ($entry) {
            return $entry->assessments->last()->overall_score;
        });

        // check against latest assessment status of projects
        CRUD::filter('assessment_status')
            ->type('select2')
            ->label('Filter by Status')
            ->options(collect(AssessmentStatus::cases())->pluck('value', 'value')->toArray())
            ->whenActive(function ($value) {
                // find each project latest assessment with specified status
                $assessments = DB::select('select project_id from assessments where assessment_status = ? and id in (select max(id) from assessments group by project_id)', [$value]);

                // get values of a column as an array
                $projectIds = array_column($assessments, 'project_id');

                // find project with founded project Id list
                $this->crud->query->whereIn("id", $projectIds);
            });

        $selectedOrganisationId = Session::get('selectedOrganisationId');
        $this->crud->addClause('where', 'organisation_id', $selectedOrganisationId);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->authorize('create', Project::class);

        CRUD::setValidation(ProjectRequest::class);

        CRUD::field('title')->type('section-title')
            ->content('Enter the key project details below.')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');

        $selectedOrganisationId = Session::get('selectedOrganisationId');
        CRUD::field('organisation_id')->type('hidden')->value($selectedOrganisationId);

        $this->crud->addFields([
            [
                'name' => 'portfolio_id',
                'type' => 'select',
                'label' => 'Portfolio',
                'model'     => "App\Models\PortFolio",
                'attribute' => 'name',
                'options'   => (function ($query) {
                    return $query->where('organisation_id', Session::get('selectedOrganisationId'))->orderBy('name', 'ASC')->get();
                }),
            ],
        ]);

        CRUD::field('name');
        CRUD::field('code')->hint('The code should uniquely identify the project within your institution\'s porfolio. Leave blank for an auto-generated code.');
        CRUD::field('description')->hint('This is optional, but will help to provide context for the AE assessment');

        CRUD::field('currency')
            ->wrapper(['class' => 'form-group col-sm-3 required'])
            ->attributes(['class' => 'form-control text-right'])
            ->hint('Enter the 3-digit code for the currency, e.g. "EUR", or "USD"');
        CRUD::field('budget')
            ->wrapper(['class' => 'form-group col-sm-9 required'])
            ->hint('Enter the overall budget for the project');

        CRUD::field('start_date')->type('date_picker')->label('Enter the start date for the project.');
        CRUD::field('end_date')->type('date_picker')->label('Enter the end date for the project.')
            ->hint('This is optional');

        CRUD::field('geo-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Geographical Reach')
            ->content('The next questions are about where the project operates. The first question is required to understand the scope of the project. You may also choose to add more information below about where your project works. This is not required but, if completed, it will allow you to explore your institutional profile by geographical area. You can choose which level is useful for your institutional analysis.');


        CRUD::field('geographic_reach')
            ->type('select2_from_array')
            ->options([
                'global' => 'Global Level',
                'multi-country' => 'Multi Country Level',
                'country' => 'Country Level'
            ]);

        CRUD::field('continents')->type('relationship')
            ->label('Select the continent / continents that this project works in.')
            ->allows_null(true);

        CRUD::field('regions')->type('relationship')
            ->label('Select the regions that this project works in')
            ->hint('Using the UN geo-scheme standard 49 list of regions/sub-regions (more information <a href="https://www.eea.europa.eu/data-and-maps/data/external/un-geoscheme-standard-m49">here</a>)')
            ->ajax(true)
            ->minimum_input_length(0)
            ->dependencies(['continents'])
            ->allows_null(true);

        CRUD::field('countries')->type('relationship')
            ->label('Select the country / countries that this project works in.')
            ->hint('Start typing to filter the results.')
            ->ajax(true)
            ->minimum_input_length(0)
            ->dependencies(['continents,regions'])
            ->allows_null(true);;

        CRUD::field('sub_regions')->type('textarea')
            ->label('Optionally, add the specific regions within each country where the project works.');

    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
        CRUD::modifyField('code', ['hint' => '', 'validationRules' => 'required', 'validationMessages' => ['required' => 'The code field is required']]);
        $this->crud->setValidation();
    }

    // create related records for a new assessment
    public function reAssess($id) {
        $assessment = Assessment::create(['project_id' => $id]);
        $assessment->redLines()->sync(RedLine::all()->pluck('id')->toArray());
        $assessment->principles()->sync(Principle::all()->pluck('id')->toArray());

        // refresh CRUD panel
        return back();
    }

    /**
     * Define what happens when the Delete operation is loaded.
     */
    public function destroy($id)
    {
        $this->authorize('delete', Project::find($id));

        $this->crud->hasAccessOrFail('delete');

        return $this->crud->delete($id);
    }


    public function fetchScoreTag()
    {
        $test = collect(request()->input('form'));
    }

    public function getImportForm()
    {
        $this->crud->hasAccessOrFail('import');
        $this->crud->setOperation('import');

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();

        $this->data['title'] = 'Import ' . $this->crud->entity_name . ' from excel file';


        $this->crud->addField([
            'name' => 'organisation',
            'label' => 'Institution',
            'type' => 'relationship',
            'validationRules' => 'required',
        ]);

        $this->crud->addField([
            'name' => 'importFile',
            'type' => 'upload',
            'label' => 'Select Excel File to Upload',
        ]);

        return view('file-util::vendor.backpack.crud.import::import', $this->data);
    }

    public function postImportForm()
    {
        $this->crud->hasAccessOrFail('import');
        $importer = $this->crud->get('import.importer');

        if (!$importer) {
            return response("Importer Class not found - please check the importer is properly setup for this page", 500);
        }

        $request = $this->crud->validateRequest();


        // pass organisation to importer;
        $organisation = Organisation::find($request->organisation);
        Excel::import(new $importer($organisation), $request->importFile);


        Alert::success(trans('backpack::crud.insert_success'))->flash();

        if ($route = $this->crud->get('import.redirect')) {
            return redirect(url($route));
        }

        return redirect(url($this->crud->route));
    }

    public function fetchCustomScoreTags1()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 1);
            }
        ]);
    }

    public function fetchCustomScoreTags2()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 2);
            }
        ]);
    }

    public function fetchCustomScoreTags3()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 3);
            }
        ]);
    }

    public function fetchCustomScoreTags4()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 4);
            }
        ]);
    }

    public function fetchCustomScoreTags5()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 5);
            }
        ]);
    }

    public function fetchCustomScoreTags6()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 6);
            }
        ]);
    }

    public function fetchCustomScoreTags7()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 7);
            }
        ]);
    }

    public function fetchCustomScoreTags8()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 8);
            }
        ]);
    }

    public function fetchCustomScoreTags9()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 9);
            }
        ]);
    }

    public function fetchCustomScoreTags10()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 10);
            }
        ]);
    }

    public function fetchCustomScoreTags11()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 11);
            }
        ]);
    }

    public function fetchCustomScoreTags12()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 12);
            }
        ]);
    }


    public function fetchCustomScoreTags13()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 13);
            }
        ]);
    }

    public function fetchRegions()
    {
        $continents = collect(request()->input('form'))
            ->filter(fn($item) => $item['name'] === 'continents[]')
            ->pluck('value')
            ->toArray();


        if (!$continents) {
            return $this->fetch(Region::class);
        }

        return $this->fetch([
            'model' => Region::class,
            'query' => function ($model) use ($continents) {
                return $model->whereIn('continent_id', $continents);
            }
        ]);
    }

    public function fetchCountries()
    {
        $continents = collect(request()->input('form'))
            ->filter(fn($item) => $item['name'] === 'continents[]')
            ->pluck('value')
            ->toArray();

        $regions = collect(request()->input('form'))
            ->filter(fn($item) => $item['name'] === 'regions[]')
            ->pluck('value')
            ->toArray();


        if (!$regions && !$continents) {
            return $this->fetch(Country::class);
        }

        if (!$regions) {
            return $this->fetch([
                'model' => Country::class,
                'query' => function ($model) use ($continents) {
                    return $model->whereHas('region', function ($query) use ($continents) {
                        $query->whereIn('continent_id', $continents);
                    });
                }
            ]);
        }


        return $this->fetch([
            'model' => Country::class,
            'query' => function ($model) use ($regions) {
                return $model->whereIn('region_id', $regions);
            }
        ]);
    }


}
