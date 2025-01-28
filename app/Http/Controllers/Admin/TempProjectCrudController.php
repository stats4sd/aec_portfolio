<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;
use App\Models\TempProject;
use App\Models\TempProjectImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Prologue\Alerts\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use Backpack\CRUD\app\Library\Widget;
use App\Imports\ProjectWorkbookImport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Imports\TempProjectWorkbookImport;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\Operations\ImportOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Exports\InitiativeImportTemplate\InitiativeImportTemplateExportWorkbook;

/**
 * Class TempProjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TempProjectCrudController extends CrudController
{
    use ListOperation {
        index as traitIndex;
    }

    use ShowOperation;

    use ImportOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(TempProject::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/temp-project');

        CRUD::setEntityNameStrings('initiative to import', 'initiatives to import');

        // specify importer class
        CRUD::set('import.importer', TempProjectWorkbookImport::class);

        // add import template file and template file filename
        CRUD::set('import.template', InitiativeImportTemplateExportWorkbook::class);
        CRUD::set('import.template-name', 'Agroecology Funding Tool - Initiative Import Template.xlsx');
        CRUD::setDefaultPageLength(50);
        CRUD::setResponsiveTable(false);

        CRUD::setHeading('Begin new import');

        if ($tempProjectImport = $this->getTempProjectImport()) {
            CRUD::setHeading("Importing into Portfolio: {$tempProjectImport->portfolio->name}");
        }

        CRUD::setListView('vendor.backpack.crud.temp-project-list');

    }

    public function index()
    {
        // get selectedOrganisationId from session
        $selectedOrganisationId = Session::get('selectedOrganisationId');

        $tempProjectImport = auth()->user()->tempProjectImports->where('organisation_id', $selectedOrganisationId)->first();

        if (!$tempProjectImport) {
            return redirect(backpack_url('project'));
        }

        return $this->traitIndex();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::column('code');
        CRUD::column('name');
        CRUD::column('valid')->type('boolean');

        $this->crud->addColumn([
            'name' => 'validation_result',
            'label' => 'Validation result',
            'type' => 'textarea',
            'priority' => 1,
            'escaped' => false,
        ]);
        $tempProjectImport = $this->getTempProjectImport();

        if (!$tempProjectImport) {
            // will be redirected;
            return;
        }

        // add import button
        // add discard import button
        $this->crud->addButton('top', 'discardImport', 'view', 'vendor.backpack.crud.buttons.discard_import', 'end');
        $this->crud->addButton('top', 'import', 'view', 'vendor.backpack.crud.buttons.re-import', 'end');

        // add finalise button
        if ($tempProjectImport->can_import) {
            $this->crud->addButton('top', 'finalise', 'view', 'vendor.backpack.crud.buttons.finalise_enabled', 'end');
        } else {
            $this->crud->addButton('top', 'finalise', 'view', 'vendor.backpack.crud.buttons.finalise_disabled', 'end');
        }

        // add a "simple" filter called Invalid to show temp_projects records cannot pass validation
        $this->crud->addFilter(
            [
                'type' => 'simple',
                'name' => 'invalid',
                'label' => 'Show only entries with validation errors',
            ],
            false,
            function () {
                $this->crud->addClause('where', 'valid', '0');
            }
        );

        // add a widget to show import summary
        if ($tempProjectImport->can_import) {

            Widget::add()->to('before_content')->type('div')->class('row')->content([
                Widget::make(
                    [
                        'type' => 'card',
                        'class' => 'card bg-white text-dark shadow-xl border-success',
                        'wrapper' => ['class' => 'col-12 col-lg-8'],
                        'content' => [
                            'body' => "{$tempProjectImport->total_records} initiatives found. You may review the entries in the table below. Click 'preview' to see the details of any initiative. You may wish to do some spot checks to ensure the data are what you expect to see.<br/><br/>
                                Once you have confirmed the data are correct, click 'finalise' to complete the import into the portfolio: <b> {$tempProjectImport->portfolio->name}</b>",
                        ],
                    ]
                ),
            ]);
        } else {

            Widget::add()->to('before_content')->type('div')->class('row')->content([
                Widget::make(
                    [
                        'type' => 'card',
                        'class' => 'card bg-white text-dark shadow-xl border-danger',
                        'wrapper' => ['class' => 'col-12 col-lg-8'],
                        'content' => [
                            'header' => 'Import Summary',
                            'body' => "
                                <b>Initiatives Found: {$tempProjectImport->total_records}</b><br/>
                                <b class='text-danger'>Initiatives Requiring Review: {$tempProjectImport->invalid_records}</b>
                                <br/><br/>Please review the validation errors in the table below, and make any required updates in your original Excel file. You can re-upload the file by clicking 'upload new file'.",
                        ],
                    ]
                ),
            ]);
        }

    }

    public function getImportForm()
    {
        $this->crud->hasAccessOrFail('import');
        $this->crud->setOperation('import');
        $tempProjectImport = $this->getTempProjectImport();

        if ($tempProjectImport) {
            $this->crud->setHeading("Import Initiatives into portfolio: {$tempProjectImport->portfolio->name}");
        }

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

        if (!$tempProjectImport) {
            // this is a first import, let user to select a portfolio
            $this->crud->addField([
                'name' => 'portfolio',
                'label' => 'Portfolio',
                'type' => 'relationship',
                'validationRules' => 'required',
            ]);
        } else {
            // this is a re-upload, show the previously selected portfolio as the only option.
            $this->crud->addField([
                'name' => 'portfolio',
                'label' => 'Portfolio',
                'type' => 'select_from_array',
                'options' => [$tempProjectImport->portfolio_id => $tempProjectImport->portfolio_name],
                'default' => $tempProjectImport->portfolio_id,
                'validationRules' => 'required',
            ]);
        }

        // get the file name of the previously uploaded excel file
        $excelFilename = '';

        if ($tempProjectImport) {
            $excelFilename = ' (' . $tempProjectImport->getMedia('project_import_excel_file')->first()->file_name . ')';
        }

        $this->crud->addField([
            'name' => 'importFile',
            'type' => 'upload',
            'label' => 'Select Excel File to Upload' . $excelFilename,
        ]);

        return view('vendor.backpack.crud.operations.import', $this->data);
    }

    public function postImportForm()
    {
        $this->crud->hasAccessOrFail('import');
        $importer = $this->crud->get('import.importer');

        if (!$importer) {
            return response("Importer Class not found - please check the importer is properly setup for this page", 500);
        }

        // form validation, to make sure user has selected a portfolio and excel file
        $request = $this->crud->validateRequest();

        Validator::make($request->all(), [
            'portfolio' => 'required',
            'importFile' => 'required',
        ])->validate();


        // find portfolio model
        $portfolio = Portfolio::find($request->portfolio);

        // get selectedOrganisationId from session
        $selectedOrganisationId = Session::get('selectedOrganisationId');

        // create or get ProjectImport model belongs to logged in user
        $tempProjectImport = TempProjectImport::firstOrCreate([
            'user_id' => auth()->user()->id,
            'organisation_id' => $selectedOrganisationId,
            'portfolio_id' => $portfolio->id,
            'portfolio_name' => $portfolio->name,
        ]);

        // remove all temp_projects records related to this user
        TempProject::where('temp_project_import_id', $tempProjectImport->id)->delete();

        // call Laravel Excel package to import excel file into temp_projects table
        Excel::import(new $importer($portfolio, $tempProjectImport), $request->importFile);

        // remove the previously attached excel file
        $tempProjectImport->clearMediaCollection('project_import_excel_file');

        // attach the uploaded excel file to TempProjectImport model
        $tempProjectImport->addMediaFromRequest('importFile')->toMediaCollection('project_import_excel_file');

        $numberOfRecords = TempProject::where('temp_project_import_id', $tempProjectImport->id)->count();

        // find number of invalid records
        $numberOfInvalidRecords = TempProject::where('temp_project_import_id', $tempProjectImport->id)->where('valid', 0)->count();

        // store user selected portfolio id for later use
        $tempProjectImport->portfolio_id = $portfolio->id;

        // store total number of invalid records and total number of records for later use
        $tempProjectImport->invalid_records = $numberOfInvalidRecords;
        $tempProjectImport->total_records = $numberOfRecords;

        // check if the uploaded file contains no error and ready for import (to show the enabled Finalise button)
        $tempProjectImport->can_import = $numberOfInvalidRecords == 0;
        $tempProjectImport->save();

        Alert::success(trans('backpack::crud.insert_success'))->flash();

        // redirect to import redirect route if specified
        if ($route = $this->crud->get('import.redirect')) {
            return redirect(url($route));
        }

        // redirect to list view
        return redirect(url($this->crud->route));
    }


    protected function setupShowOperation()
    {
        // add a widget to show validation result with color and multiple lines
        if ($this->crud->getCurrentEntry()->valid) {

            Widget::add()->to('before_content')->type('div')->class('row')->content([

                Widget::make(
                    [
                        'type' => 'card',
                        'class' => 'card bg-primary text-white',
                        'wrapper' => ['class' => 'col-sm-4 col-md-8'],
                        'content' => [
                            'header' => 'Validation result',
                            'body' => 'The initiative data is correct.',
                        ],
                    ]
                ),
            ]);
        } else {

            Widget::add()->to('before_content')->type('div')->class('row')->content([

                Widget::make(
                    [
                        'type' => 'card',
                        'class' => 'card bg-error text-white',
                        'wrapper' => ['class' => 'col-sm-4 col-md-8'],
                        'content' => [
                            'header' => 'Validation result',
                            'body' => $this->crud->getCurrentEntry()->validation_result,
                        ],
                    ]
                ),
            ]);
        }

        CRUD::column('code');
        CRUD::column('name');

        CRUD::column('category');
        CRUD::column('description');
        CRUD::column('currency');
        CRUD::column('exchange_rate');
        CRUD::column('exchange_rate_eur');
        CRUD::column('budget');
        CRUD::column('uses_only_own_funds');
        CRUD::column('main_recipient');
        CRUD::column('start_date');
        CRUD::column('end_date');
        CRUD::column('geographic_reach');
        CRUD::column('continents');
        CRUD::column('regions');
        CRUD::column('countries');
    }

    // when all rows of project data are correct in the uploaded excel file, it is now ready to import excel file to projects records
    public function finalise()
    {
        $tempProjectImport = $this->getTempProjectImport();

        if ($tempProjectImport) {
            $importer = ProjectWorkbookImport::class;
            $portfolio = Portfolio::find($tempProjectImport->portfolio_id);
            $excelFile = $tempProjectImport->getMedia('project_import_excel_file')->first()->getPath();

            // call Laravel Excel package to import the uploaded excel file into projects table

            try {

                Excel::import(new $importer($portfolio), $excelFile);

            } catch (ValidationException $e) {
                dd($e);
            }

            // remove all temp_projects records related to this user
            TempProject::where('temp_project_import_id', $tempProjectImport->id)->delete();

            // remove the previously attached excel file
            $tempProjectImport->clearMediaCollection('project_import_excel_file');

            // remove TempProjectImport model
            $tempProjectImport->delete();
        }

        // redirect to Initiative page, list view
        return redirect('/admin/project');
    }

    // discard import to remove temp_projects records and temp_project_import models
    public function discardImport()
    {

        if ($tempProjectImport = $this->getTempProjectImport()) {
            // remove all temp_projects records related to this user
            TempProject::where('temp_project_import_id', $tempProjectImport->id)->delete();

            $tempProjectImport->delete();
        }

        // redirect to Initiative page, list view
        return redirect('/admin/project');
    }

    protected function getTempProjectImport(): ?TempProjectImport
    {
        // get selectedOrganisationId from session
        $selectedOrganisationId = Session::get('selectedOrganisationId');

        return auth()->user()->tempProjectImports->where('organisation_id', $selectedOrganisationId)->first();
    }
}
