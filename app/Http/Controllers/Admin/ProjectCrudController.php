<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use App\Models\Country;
use App\Models\RedLine;
use App\Models\ScoreTag;
use App\Models\Portfolio;
use App\Models\Principle;
use Illuminate\Support\Str;
use App\Models\Organisation;
use App\Imports\ProjectImport;
use App\Models\CustomScoreTag;
use App\Enums\AssessmentStatus;
use App\Enums\GeographicalReach;
use App\Models\OrganisationMember;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProjectRequest;
use Backpack\CRUD\app\Library\Widget;
use function mysql_xdevapi\getSession;
use phpDocumentor\Reflection\Types\True_;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\Operations\AssessOperation;
use App\Http\Controllers\Admin\Operations\ImportOperation;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use App\Http\Controllers\Admin\Traits\UsesSaveAndNextAction;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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

    use ShowOperation;
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
        CRUD::setModel(\App\Models\Project::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project');
        CRUD::setEntityNameStrings('initiative', 'initiatives');

        CRUD::set('import.importer', ProjectImport::class);
        CRUD::set('import.template-path', 'AE Marker - Project Import Template.xlsx');

        CRUD::setShowView('projects.show');

    }

    public function show($id)
    {
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
        $this->data['spiderData'] = $this->data['entry']->principleProjects->map(function ($principleProject) {
            return [
                'axis' => $principleProject->principle->name,
                'value' => $principleProject->rating,
            ];
        });

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getShowView(), $this->data);
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
        CRUD::setResponsiveTable(false);
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
        CRUD::column('assessment_status')->type('closure')->function(function ($entry) {
            return $entry->assessment_status?->value;
        });
        CRUD::column('overall_score')->type('number')->decimals(1)->suffix('%');

        CRUD::filter('assessment_status')
            ->type('select2')
            ->label('Filter by Status')
            ->options(collect(AssessmentStatus::cases())->pluck('value', 'value')->toArray())
            ->active(function ($value) {
                $this->crud->query->where('assessment_status', $value);
            });


        if (Auth::user()->hasRole('Site Admin')) {
            // institution filter with all institutions
            CRUD::filter('organisation_id')
                ->type('select2')
                ->label('Filter by Institution')
                ->options(Organisation::all()->pluck('name', 'id')->toArray())
                ->active(function ($value) {
                    $this->crud->query->where('organisation_id', $value);
                });

            $selectedOrgnisationId = request()->get('organisation_id');

            // no organisation is selected, show all portfolios in portfolio filter
            if ($selectedOrgnisationId == null) {
                CRUD::filter('portfolio_id')
                ->type('select2')
                ->label('Filter by Portfolio')
                ->options(Portfolio::all()->pluck('name', 'id')->toArray())
                ->active(function ($value) {
                    $this->crud->query->where('portfolio_id', $value);
                });

            // one organisation is selected, show portfolios belong to the selected organisation in portfolio filter
            // Question: it works only after reloading the page... Is there any way to take effect without reloading this page?
            } else {
                CRUD::filter('portfolio_id')
                ->type('select2')
                ->label('Filter by Portfolio')
                ->options(Portfolio::where('organisation_id', $selectedOrgnisationId)->pluck('name', 'id')->toArray())
                ->active(function ($value) {
                    $this->crud->query->where('portfolio_id', $value);
                });
            }

        } else {

            // find the organisations that user belongs to
            $userOrganisationIds = OrganisationMember::select('organisation_id')->where('user_id', Auth::user()->id)->get();

            // show institution filter if user belong to more than one institution
            if (count($userOrganisationIds) > 1) {
                // institution filter with all institutions
                CRUD::filter('organisation_id')
                    ->type('select2')
                    ->label('Filter by Institution')
                    ->options(Organisation::whereIn('id', $userOrganisationIds)->pluck('name', 'id')->toArray())
                    ->active(function ($value) {
                        $this->crud->query->where('organisation_id', $value);
                    });
            }

            // portfolio filter with portfolios that belong to user's institutions
            // TODO: show portfolios that belong to the selected institution
            if (count($userOrganisationIds) >= 1) {
                CRUD::filter('portfolio_id')
                    ->type('select2')
                    ->label('Filter by Portfolio')
                    ->options(Portfolio::whereIn('organisation_id', $userOrganisationIds)->pluck('name', 'id')->toArray())
                    ->active(function ($value) {
                        $this->crud->query->where('portfolio_id', $value);
                    });
            }

        }

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

        CRUD::field('title')->type('section-title')
            ->content('Enter the key project details below.')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields');

        if (Auth::user()?->hasRole('Site Admin') || Auth::user()?->organisations()->count() > 1) {
            CRUD::field('organisation_id')->type('relationship')->label('Institution');

            // add portfolio selection box
            // Question: how to show portfolios for the selected institution?
            CRUD::field('portfolio_id')->type('relationship')->label('Portfolio');

        } else {
            $organisation = Auth::user()?->organisations()->first();
            CRUD::field('organisation_id')->type('hidden')->value($organisation->id);
            CRUD::field('organisation_title')->type('section-title')->view_namespace('stats4sd.laravel-backpack-section-title::fields')->content('Creating a Project for <b>' . $organisation->name . '</b>');

            // TODO: add portfolio selection box
            // Question: how to show portfolios for the only one institution that user belongs to
            CRUD::field('portfolio_id')->type('relationship')->label('Portfolio');
        }

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
        $this->setupCreateOperation();
        CRUD::modifyField('code', ['hint' => '', 'validationRules' => 'required', 'validationMessages' => ['required' => 'The code field is required']]);
        $this->crud->setValidation();
    }

    public function setupAssessOperation()
    {
        Widget::add()->type('script')->content('assets/js/admin/forms/project_assess.js');

        CRUD::field('section-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title(function ($entry) {
                return "Assess Project: " . $entry->name;
            })
            ->content('
                    This is the main section of the review. Below are the 13 Agroecology Principles, and you should rate the project against each one. <br/><br/>
                    For each principle, you should give:
                        <ul>
                            <li><b>A rating:</b> This is a number between 0 and 2, based on your appreciation of the value of the principle in the project design / activities, and following the Spectrum defined for each principle. Decimal digits are allowed.</li>
                            <li><b>A comment:</b> Please add any comments to help explain the rating, and about how the principle is seen within the project.</li>
                        </ul>
                    Each principle also lists a set of example activities relevant to that principle. Please tick all activities that are present in the project.<br/><br/>
                    To help track progress, once a rating is given for a principle, that principle name will turn green. Once you have completed and reviewed every principle, please proceed to the final "Confirmation" tab, where you can mark the assessment as complete. Once done, you will return to the main projects list to view the final result.
          ');


        CRUD::enableVerticalTabs();
        // cannot use relationship with repeatable because we need to filter the scoretags...
        $entry = CRUD::getCurrentEntry();

        foreach ($entry->principles as $principle) {
            $ratingZeroDefintionRow = '<span class="text-secondary">This principle cannot be marked as not applicable</span>';
            if ($principle->can_be_na) {
                $ratingZeroDefintionRow = "
                                            <tr>
                                                <td>na</td>
                                                <td>{$principle->rating_na}</td>
                                            </tr>";
            }

            CRUD::field($principle->id . '_title')
                ->tab($principle->name)
                ->type('section-title')
                ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
                ->title($principle->name)
                ->content("<table class='table table - striped'>
                                <tr>
                                    <th>Score</th>
                                    <th>Definition</th>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>{$principle->rating_two}</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>{$principle->rating_one}</td>
                                </tr>
                                <tr>
                                    <td>0</td>
                                    <td>{$principle->rating_zero}</td>
                                </tr>
                                {$ratingZeroDefintionRow}
                            </table>");

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
                ->hint('Please add a comment, even if the principle is not applicable to this project.')
                ->type('textarea')
                ->default($principle->pivot->rating_comment);

            CRUD::field('scoreTags' . $principle->id)
                ->tab($principle->name)
                ->label('Presence of Examples/Indicators for ' . $principle->name)
                ->type('checklist_filtered')
                ->number_of_columns(1)
                ->model(ScoreTag::class)
                ->options(function ($query) use ($principle) {
                    return $query->where('principle_id', $principle->id)->get()->pluck('name', 'id')->toArray();
                });

            CRUD::field('customScoreTags' . $principle->id)
                ->tab($principle->name)
                ->label('New Example/Indicator for ' . $principle->name)
                ->type('table')
                ->columns([
                    'name' => 'Name',
                    'description' => 'Description (optional)'],
                );
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

        Widget::add()->type('script')->content('assets/js/admin/forms/project_redlines.js');


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
                    These are the Red Line elements, which are counter-productive or harmful to the values and principles of agroecology. If any one of these is present in the project being rated, then the Agroecology Overall Score is 0.
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
                ->type('radio')
                ->attributes([
                    'data-required' => '1',
                ])
                ->wrapper([
                    'class' => 'col-md-6',
                    'data-required-wrapper' => '1',
                ])
                ->options([
                    1 => 'Yes',
                    0 => 'No',
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
            ->label('I confirm the Redlines assessment is complete')
            ->default($entry->assessment_status !== AssessmentStatus::NotStarted && $entry->assessment_status !== AssessmentStatus::RedlinesIncomplete);

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
