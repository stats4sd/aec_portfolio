<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ProjectWorkbookImport;
use App\Models\Region;
use App\Models\Country;
use App\Models\Project;
use App\Models\RedLine;
use App\Models\AdditionalCriteriaScoreTag;
use App\Models\Portfolio;
use App\Models\Principle;
use App\Models\Assessment;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
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
class ProjectCrudController extends CrudController
{
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation {
        destroy as traitDestroy;
    }
    use ShowOperation;

    use AuthorizesRequests;

    use ImportOperation;
    use RedlineOperation;
    use FetchOperation;
    use UsesSaveAndNextAction;

    public function setup()
    {

        CRUD::setModel(Project::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project');
        CRUD::setEntityNameStrings('initiative', 'initiatives');

        CRUD::set('import.importer', ProjectWorkbookImport::class);
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
                'model' => "App\Models\PortFolio",
                'attribute' => 'name',
                'options' => (function ($query) {
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
            ->options(Arr::mapWithKeys(GeographicalReach::cases(), fn($enum) => [$enum->name => $enum->value]));

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
            ->allows_null(true);

        CRUD::field('sub_regions')->type('textarea')
            ->label('Optionally, add the specific regions within each country where the project works.');


        $this->removeAllSaveActions();
        $this->addSaveAndReturnToProjectListAction();

    }

    protected function setupUpdateOperation()
    {
        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
        CRUD::modifyField('code', ['hint' => '', 'validationRules' => 'required', 'validationMessages' => ['required' => 'The code field is required']]);
        $this->crud->setValidation();
    }

    // create related records for a new assessment
    public function reAssess($id)
    {
        $assessment = Assessment::create(['project_id' => $id]);
        $assessment->redLines()->sync(RedLine::all()->pluck('id')->toArray());
        $assessment->principles()->sync(Principle::all()->pluck('id')->toArray());

        // refresh CRUD panel
        return back();
    }

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
            'name' => 'portfolio',
            'label' => 'Portfolio',
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


        Validator::make($request->all(), [
            'portfolio' => 'required',
            'importFile' => 'required',
        ])->validate();


        // pass organisation to importer;
        $portfolio = Portfolio::find($request->portfolio);
        Excel::import(new $importer($portfolio), $request->importFile);


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
