<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use App\Models\Country;
use App\Models\Project;
use App\Models\RedLine;
use App\Models\ScoreTag;
use App\Models\Portfolio;
use App\Models\Principle;
use App\Models\Assessment;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Organisation;
use App\Models\FundingSource;
use App\Imports\ProjectImport;
use App\Models\CustomScoreTag;
use App\Enums\AssessmentStatus;
use App\Enums\GeographicalReach;
use App\Models\AdditionalCriteria;
use App\Models\OrganisationMember;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Models\PrincipleAssessment;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProjectRequest;
use Backpack\CRUD\app\Library\Widget;
use App\Imports\ProjectWorkbookImport;
use function mysql_xdevapi\getSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\True_;
use App\Models\AdditionalCriteriaScoreTag;
use App\Models\AdditionalCriteriaAssessment;
use App\Models\AdditionalCriteriaCustomScoreTag;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Admin\Operations\AssessOperation;
use App\Http\Controllers\Admin\Operations\ImportOperation;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use App\Http\Controllers\Admin\Traits\UsesSaveAndNextAction;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use App\Exports\InitiativeImportTemplate\InitiativeImportTemplateExportWorkbook;
use App\Models\Continent;

class ProjectCrudController extends CrudController
{
    use CreateOperation {
        store as traitStore;
    }

    use UpdateOperation {
        update as traitUpdate;
    }

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
        CRUD::set('import.template', InitiativeImportTemplateExportWorkbook::class);
        CRUD::set('import.template-name', 'Agroecology Funding Tool - Initiative Import Template.xlsx');

        CRUD::setShowView('projects.show');
    }

    public function show($id)
    {
        //$this->authorize('view', Project::find($id));

        //$this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry (include softDeleted items if the trait is used)
        if ($this->crud->get('show.softDeletes') && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->crud->model))) {
            $this->data['entry'] = $this->crud->getModel()
                ->withoutGlobalScopes('organisation')
                ->withTrashed()
                ->findOrFail($id);
        } else {
            $this->data['entry'] = Project::withoutGlobalScopes(['organisation'])->find($id);
        }

        $this->data = self::getShowData($this->data);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getShowView(), $this->data);
    }

    public static function getShowData(array $data)
    {
        $data['entry']->load([
            'assessments' => [
                'failingRedLines',
            ],
        ]);

        // #### ADD SPIDER CHART DATA ###
        $data['spiderData'] = $data['entry']->assessments->last()->principleAssessments->map(function ($principleProject) {
            return [
                'axis' => $principleProject->principle->name,
                'value' => $principleProject->rating,
            ];
        });

        return $data;
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
        Widget::add()->type('script')
            ->content('assets/js/admin/forms/project_create.js');

        $this->authorize('create', Project::class);

        CRUD::setValidation(ProjectRequest::class);

        CRUD::field('title')->type('section-title')
            ->content('Enter the key project details below.')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');

        $selectedOrganisationId = Session::get('selectedOrganisationId');
        CRUD::field('organisation_id')->type('hidden')->value($selectedOrganisationId);

        $selectedOrganisation = Organisation::find($selectedOrganisationId);

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
        CRUD::field('initiativeCategory')->label('Select the initiative category.')
            ->hint('Select the one that best matches. If none of the options fit, select "other".');
        CRUD::field('initiative_category_other')->label('Enter the "other" category of initiative.');
        CRUD::field('description')->hint('This is optional, but will help to provide context for the AE assessment');

        CRUD::field('initiative-timing-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Initiative Timing');


        CRUD::field('start_date')->type('date')->label('Enter the start date for the project.');
        CRUD::field('end_date')->type('date')->label('Enter the end date for the project.')
            ->hint('This is optional');

        CRUD::field('currency-info')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Currency and Budget')
            ->content("$selectedOrganisation->name uses $selectedOrganisation->currency as the default currency. You may change the currency for this initiative if you wish. For analysis, the budget will be converted into $selectedOrganisation->currency. The platform also requires initiave budgets in EUR, to enable anonymous cross-organisation analysis. Below you can enter the budget, the initiative's currency, and the exchange rate to convert to $selectedOrganisation->currency and EUR (if needed).
            <br/><br/>
            The Platform can automatically convert the most common currencies using the exchange rate for the initiative's start date (or today, if the initiative start is in the future).
            <br/><br/>
            For less commonly used currencies, or if you know the exchange rate to use, you can enter a custom exchange rate below.
             <br/><br/>
            <div class='btn btn-link' id='currencyListLabel' data-toggle='modal' data-target='#currencyList'><i class='la la-info-circle'></i> Which currencies can be automatically converted?</div>

            <div class='modal fade' id='currencyList' tabindex=' - 1' aria-labelledby='currencyListLabel' aria-hidden='true'>
              <div class='modal-dialog'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h5 class='modal-title' id='exampleModalLabel'>Currencies</h5>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                      <span aria-hidden='true'>&times;</span>
                    </button>
                  </div>
                  <div class='modal-body'>
                    The platform currently has exchange rate information available for the following currencies:
                    <ul>
                        <li>EUR	Euro</li>
                        <li>USD	(US Dollar)</li>
                        <li>JPY	(Japanese Yen)</li>
                        <li>BGN	(Bulgarian Lev)</li>
                        <li>CZK	(Czech Republic Koruna)</li>
                        <li>DKK	(Danish Krone)</li>
                        <li>GBP	(British Pound Sterling)</li>
                        <li>HUF	(Hungarian Forint)</li>
                        <li>PLN	(Polish Zloty)</li>
                        <li>RON	(Romanian Leu)</li>
                        <li>SEK	(Swedish Krona)</li>
                        <li>CHF	(Swiss Franc)</li>
                        <li>ISK	(Icelandic Króna)</li>
                        <li>NOK	(Norwegian Krone)</li>
                        <li>HRK	(Croatian Kuna)</li>
                        <li>RUB	(Russian Ruble)</li>
                        <li>TRY	(Turkish Lira)</li>
                        <li>AUD	(Australian Dollar)</li>
                        <li>BRL	(Brazilian Real)</li>
                        <li>CAD	(Canadian Dollar)</li>
                        <li>CNY	(Chinese Yuan)</li>
                        <li>HKD	(Hong Kong Dollar)</li>
                        <li>IDR	(Indonesian Rupiah)</li>
                        <li>ILS	(Israeli New Sheqel)</li>
                        <li>INR	(Indian Rupee)</li>
                        <li>KRW	(South Korean Won)</li>
                        <li>MXN	(Mexican Peso)</li>
                        <li>MYR	(Malaysian Ringgit)</li>
                        <li>NZD	(New Zealand Dollar)</li>
                        <li>PHP	(Philippine Peso)</li>
                        <li>SGD	(Singapore Dollar)</li>
                        <li>THB	(Thai Baht)</li>
                        <li>ZAR	(South African Rand)</li>
                    </ul>
                  </div>
                  <div class='modal-footer'>
                    <button type='button' class='btn btn - secondary' data-dismiss='modal'>Close</button>
                  </div>
                </div>
              </div>
            </div>

 ");

        CRUD::field('currency')
            ->wrapper(['class' => 'form-group col-sm-4 required'])
            ->attributes(['class' => 'form-control text-right'])
            ->default($selectedOrganisation->currency)
            ->hint('Enter the 3-digit code for the currency, e.g. "EUR", or "USD"');

        // change budget field to a hidden field
        CRUD::field('budget')
            ->type('hidden')
            // ->wrapper(['class' => 'form-group col-sm-8 required'])
            ->hint('Enter the overall budget for the project');

        // use displayBudget to accept budget with thousand separators
        CRUD::field('displayBudget')
            ->label('Budget')
            ->wrapper(['class' => 'form-group col-sm-8 required'])
            ->hint('Enter the overall budget for the project');

        CRUD::field('exchange_rate_title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Exchange Rate')
            ->content("To attempt to get the exchange rates automatically, click the button below. You will have the option to edit the rates. Alternatively, you may enter the exchange rates manually below.");

        CRUD::field('get_exchange_rate_button')
            ->type('custom_html')
            ->wrapper(['class' => 'form-group col-sm-4'])
            ->value('
                <div class="d-flex flex-column align-items-center">
                <div class="btn btn-primary" onclick="getExchangeRate()">Get Exchange Rate(s)</div>
                </div>
            ');

        CRUD::field('org_currency')
            ->type('hidden')
            ->value($selectedOrganisation->currency);

        CRUD::field('exchange_rate')
            ->label("Exchange rate from the initiative's currency to  $selectedOrganisation->currency (Organisation currency)")
            ->hint('1 of this initiative\'s currency = XXX ' . $selectedOrganisation->currency . '.')
            ->type('number')
            ->attributes(['step' => 'any'])
            ->wrapper(['class' => 'form-group col-sm-12']);


        CRUD::field('exchange_rate_eur')
            ->label("Exchange rate from the initiative's currency to EUR")
            ->hint('1 of this initiative\'s currency = XXX EUR.')
            ->type('number')
            ->attributes(['step' => 'any'])
            ->wrapper(['class' => 'form-group col-sm-12']);


        CRUD::field('budget_eur')
            ->type('hidden');

        CRUD::field('funding_sources_title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Funding Source and Recipient')
            ->content("To enable a high-level analysis of funding flows towards Agroecology, please answer the following questions.");

        CRUD::field('uses_only_own_funds')
            ->type('radio')
            ->label('Is this initiative entirely funded through your own institutional funds, or are there external funding sources?')
            ->options([
                0 => 'This initiative is <b>entirely</b> self-funded',
                1 => 'This initiative has external funding sources',
            ]);

        CRUD::field('fundingSources')
            ->label('Funding Sources')
            ->new_item_label('Add funding source')
            ->type('relationship')
            ->attribute('source')
            ->subfields([
                [
                    'name' => 'institution_id',
                    'type' => 'relationship',
                    'label' => 'Select the funding source...',
                    'ajax' => true,
                    'data_source' => url('/admin/project/fetch/funding-sources'),
                    'minimum_input_length' => 0,
                    'wrapper' => ['class' => 'form-group col-md-6'],
                ],
                [
                    'name' => 'source',
                    'type' => 'text',
                    'label' => '...or type in the name here',
                    'wrapper' => ['class' => 'form-group col-md-6'],
                ],
                [
                    'name' => 'amount',
                    'type' => 'number',
                    'label' => 'Enter the amount of funding given by this source',
                ],
            ]);

        CRUD::field('main_recipient')
            ->type('textarea')
            ->label('Please enter the main recipient of the funds for this initiative')
            ->hint('E.g., the institution or entity that directly receives the majority of the funds for this initiative');


        CRUD::field('geo-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Geographical Reach')
            ->content('The next questions are about where the project operates. This information is important as it will allow you to compare your own initiatives based on region/country with other institutions\' initiatives in those same locations.');


        CRUD::field('geographic_reach')
            ->type('select2_from_array')
            ->options(Arr::mapWithKeys(GeographicalReach::cases(), fn($enum) => [$enum->value => $enum->value]));

        CRUD::field('continents')->type('relationship')
            ->label('Select the continent / continents that this project works in.')
            ->allows_null(true);

        CRUD::field('has_all_regions')->type('checkbox')
            ->label('Does this project work in all the regions in the chosen continents?')
            ->hint('If this box is ticked, the project will be assigned to every region from the chosen continents when you save your changes.');

        CRUD::field('regions')->type('relationship')
            ->label('Select the regions that this project works in')
            ->hint('Using the UN geo-scheme standard 49 list of regions/sub-regions (more information <a href="https://www.eea.europa.eu/data-and-maps/data/external/un-geoscheme-standard-m49">here</a>)')
            ->ajax(true)
            ->minimum_input_length(0)
            ->dependencies(['continents'])
            ->allows_null(true);

        CRUD::field('has_all_countries')->type('checkbox')
            ->label('Does this project work in all the countries in the chosen regions?')
            ->hint('If this box is ticked, the project will be assigned to every country from the chosen regions when you save your changes.');

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
        $currentOrgId = Session::get('selectedOrganisationId');
        $currentOrg = Organisation::where('id', $currentOrgId)->first();
        $assessment->additionalCriteria()->sync($currentOrg->additionalCriteria->pluck('id')->toArray());

        $project = Project::find($id)?->load([
            'portfolio' => [
                'organisation',
            ],
            'assessments' => [
                'principles',
                'failingRedlines',
                'additionalCriteria',
            ],
        ])
            ->append('latest_assessment');

        return $project;
    }

    public function duplicate(Project $project)
    {
        $this->authorize('create', Project::class);

        $clone = $project->replicate();

        // do not automatically create a new assessment - the existing assessment(s) will be copied over.
        $clone->saveQuietly();

        // *** Handle belongsToMany relationships
        $continents = $project->continents;
        $regions = $project->regions;
        $countries = $project->countries;


        $clone->continents()->sync($continents->pluck('id')->toArray());
        $clone->regions()->sync($regions->pluck('id')->toArray());
        $clone->countries()->sync($countries->pluck('id')->toArray());


        // *** Handle 'has Many' relationships:
        // duplicate funding sources
        $project->fundingSources->each(function (FundingSource $fundingSource) use ($clone) {
            unset($fundingSource->id);
            unset($fundingSource->project_id);

            $clone->fundingSources()->create($fundingSource->toArray());
        });


        // duplicate assessments
        $project->assessments->load([
            'principleAssessments.scoreTags',
            'principleAssessments.customScoreTags',
            'additionalCriteriaAssessment.scoreTags',
            'additionalCriteriaAssessment.customScoreTags',
        ])
            ->each(function (Assessment $assessment) use ($clone) {
                $newAssessment = $assessment->replicate();
                $newAssessment->project_id = $clone->id;
                $newAssessment->save();


                $redlinesWithPivot = $assessment->redlines->mapWithKeys(function (Redline $redline) {
                    return [
                        $redline->id => ['value' => $redline->pivot->value],
                    ];
                });

                $newAssessment->redLines()->sync($redlinesWithPivot->toArray());


                // iterate through the principleAssessments and create new ones (along with score tags and custom score tags)
                $assessment->principleAssessments->each(function (PrincipleAssessment $principleAssessment) use ($newAssessment) {

                    unset($principleAssessment->id);
                    unset($principleAssessment->assessment_id);
                    unset($principleAssessment->created_at);
                    unset($principleAssessment->updated_at);

                    $newPrincipleAssessment = $newAssessment->principleAssessments()->create($principleAssessment->toArray());

                    // for each scoreTag linked to the original PrincipleAssessment, also sync it to the new PrincipleAssessment
                    $scoreTagsWithPivot = $principleAssessment->scoreTags->mapWithKeys(function (ScoreTag $scoreTag) use ($newAssessment) {
                        return [
                            $scoreTag->id => [
                                'assessment_id' => $newAssessment->id,
                            ],
                        ];
                    });

                    $newPrincipleAssessment->scoreTags()->sync($scoreTagsWithPivot->toArray());

                    $principleAssessment->customScoreTags->each(function (CustomScoreTag $customScoreTag) use ($newPrincipleAssessment) {
                        unset($customScoreTag->id);
                        unset($customScoreTag->principle_assessment_id);
                        unset($customScoreTag->created_at);
                        unset($customScoreTag->updated_at);

                        $customScoreTag->assessment_id = $newPrincipleAssessment->assessment_id;

                        $newPrincipleAssessment->customScoreTags()->create($customScoreTag->toArray());
                    });
                });

                $assessment->additionalCriteriaAssessment->each(function (AdditionalCriteriaAssessment $additionalCriteriaAssessment) use ($newAssessment) {

                    unset($additionalCriteriaAssessment->id);
                    unset($additionalCriteriaAssessment->assessment_id);
                    unset($additionalCriteriaAssessment->created_at);
                    unset($additionalCriteriaAssessment->updated_at);

                    $newAdditionalCriteriaAssessment = $newAssessment->additionalCriteriaAssessment()->create($additionalCriteriaAssessment->toArray());

                    $scoreTagsWithPivot = $additionalCriteriaAssessment->scoreTags->mapWithKeys(function (AdditionalCriteriaScoreTag $scoreTag) use ($newAssessment) {
                        return [
                            $scoreTag->id => [
                                'assessment_id' => $newAssessment->id,
                            ],
                        ];
                    });

                    $newAdditionalCriteriaAssessment->scoreTags()->sync($scoreTagsWithPivot);

                    $additionalCriteriaAssessment->customScoreTags->each(function (AdditionalCriteriaCustomScoreTag $customScoreTag) use ($newAdditionalCriteriaAssessment) {
                        unset($customScoreTag->id);
                        unset($customScoreTag->additional_criteria_assessment_id);
                        unset($customScoreTag->created_at);
                        unset($customScoreTag->updated_at);

                        $customScoreTag->assessment_id = $newAdditionalCriteriaAssessment->assessment_id;

                        $newAdditionalCriteriaAssessment->customScoreTags()->create($customScoreTag->toArray());
                    });
                });
            });

        // as the clone was created without events; save it again to trigger any "on save" events
        $clone->save();

        return $clone;
    }

    public function store()
    {
        $this->calculateBudget();

        $this->calculateBudgetEur();

        $this->handleAllRegionSelection();

        $this->handleAllCountrySelection();

        return $this->traitStore();
    }

    public function update()
    {
        $this->calculateBudget();

        $this->calculateBudgetEur();

        $this->handleAllRegionSelection();

        $this->handleAllCountrySelection();

        return $this->traitUpdate();
    }

    // convert displayBudget to a number, set it to budget
    public function calculateBudget()
    {
        // get display budget with thousand separator
        $displayBudget = $this->crud->getRequest()->displayBudget;

        // possible improvement:
        // when displayBudget lost focus, show error message if it is not a number

        // remove possible thousand separators, e.g. comma, dot
        $budget = Str::replace(',', '', $displayBudget);
        $budget = Str::replace('.', '', $budget);

        // check if displayBudget can be converted into a number
        // to keep it simple, return 0 if it is not a number
        if (!ctype_digit($budget)) {
            $budget = 0;
        }

        $this->crud->getRequest()->request->set('budget', $budget);
    }

    public function calculateBudgetEur()
    {
        $budget = $this->crud->getRequest()->budget;
        $exchangeRateEur = $this->crud->getRequest()->exchange_rate_eur;
        $this->crud->getRequest()->request->set('budget_eur', $budget * $exchangeRateEur);
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
            'name' => 'import-template',
            'type' => 'section-title',
            'view_namespace' => 'stats4sd.laravel-backpack-section-title::fields',
            'title' => 'Import Initiatives from Excel File',
            'content' => '
            Instead of manually entering details for individual initiatives, you may choose to import them in bulk, and then add additional details using the edit feature within the platform. To ensure a successful import, please download the template provided below, and ensure your Excel file is in the correct format. The template file includes an example initiative.
            <br/><br/>
            <a href="' . url($this->crud->route . '/import-template') . '" class="btn btn-link" data-button-type="import-template"><i class="la la-download"></i> Download Template for Imports</a></br>

            ',
        ]);


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
            },
        ]);
    }

    public function fetchCustomScoreTags2()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 2);
            },
        ]);
    }

    public function fetchCustomScoreTags3()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 3);
            },
        ]);
    }

    public function fetchCustomScoreTags4()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 4);
            },
        ]);
    }

    public function fetchCustomScoreTags5()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 5);
            },
        ]);
    }

    public function fetchCustomScoreTags6()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 6);
            },
        ]);
    }

    public function fetchCustomScoreTags7()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 7);
            },
        ]);
    }

    public function fetchCustomScoreTags8()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 8);
            },
        ]);
    }

    public function fetchCustomScoreTags9()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 9);
            },
        ]);
    }

    public function fetchCustomScoreTags10()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 10);
            },
        ]);
    }

    public function fetchCustomScoreTags11()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 11);
            },
        ]);
    }

    public function fetchCustomScoreTags12()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 12);
            },
        ]);
    }


    public function fetchCustomScoreTags13()
    {
        return $this->fetch([
            'model' => CustomScoreTag::class,
            'query' => function ($model) {
                return $model->where('principle_id', 13);
            },
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
            },
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
                },
            ]);
        }


        return $this->fetch([
            'model' => Country::class,
            'query' => function ($model) use ($regions) {
                return $model->whereIn('region_id', $regions);
            },
        ]);
    }

    public function fetchFundingSources()
    {

        $currentOrgId = Session::get('selectedOrganisationId');

        return $this->fetch([
            'model' => Organisation::class,
            'query' => function (Organisation $model) use ($currentOrgId) {
                return $model->withoutGlobalScopes(['owned'])
                    ->whereNot('id', $currentOrgId);
            },
        ]);
    }

    private function handleAllRegionSelection()
    {
        $hasAllRegions = $this->crud->getRequest()->has_all_regions;

        if ($hasAllRegions) {

            $continents = $this->crud->getRequest()->continents;

            $regions = Region::whereHas('continent', function ($query) use ($continents) {
                $query->whereIn('continents.id', $continents);
            })
                ->get()
                ->pluck('id')
                ->toArray();

            $this->crud->getRequest()->request->set('regions', $regions);
        }
    }

    private function handleAllCountrySelection()
    {
        $hasAllCountries = $this->crud->getRequest()->has_all_countries;

        if ($hasAllCountries) {

            $regions = $this->crud->getRequest()->regions;

            // Error handling for below combination
            // 1. has_all_regions is false (user does not want to include all regions) AND
            // 2. regions selection box is empty (user does not choose any region) AND
            // 3. has_all_countries is true (user want to include all countries from region, but there is no region chosen...)
            //
            // It is not possible to find any country from no region...
            // return null for countries to avoid throwing error, appropriate validation message will be showed in front end
            $countries = null;

            if ($regions) {
                $countries = Country::whereHas('region', function ($query) use ($regions) {
                    $query->whereIn('regions.id', $regions);
                })
                    ->get()
                    ->pluck('id')
                    ->toArray();
            }

            $this->crud->getRequest()->request->set('countries', $countries);
        }
    }
}
