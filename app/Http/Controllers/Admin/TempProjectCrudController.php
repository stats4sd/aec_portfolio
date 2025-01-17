<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;
use Prologue\Alerts\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProjectWorkbookImport;
use App\Http\Requests\TempProjectRequest;
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
    use ListOperation;
    use ShowOperation;
    use ImportOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\TempProject::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/temp-project');
        CRUD::setEntityNameStrings('temp project', 'temp projects');

        // specify importer class
        // TODO: change to TempProjectWorkbookImport
        CRUD::set('import.importer', ProjectWorkbookImport::class);
        // CRUD::set('import.importer', TempProjectWorkbookImport::class);

        // add import template file and template file filename
        CRUD::set('import.template', InitiativeImportTemplateExportWorkbook::class);
        CRUD::set('import.template-name', 'Agroecology Funding Tool - Initiative Import Template.xlsx');

        // TODO: add validation request class
        // set validation request class in function setupCreateOperation() originally
        CRUD::setValidation(TempProjectRequest::class);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::column('project_import_id');

        CRUD::column('code');
        CRUD::column('name');

        // CRUD::column('category');
        // CRUD::column('description');
        // CRUD::column('currency');
        // CRUD::column('exchange_rate');
        // CRUD::column('exchange_rate_eur');
        // CRUD::column('budget');
        // CRUD::column('uses_only_own_funds');
        // CRUD::column('main_recipient');
        // CRUD::column('start_date');
        // CRUD::column('end_date');
        // CRUD::column('geographic_reach');
        // CRUD::column('continent_1');
        // CRUD::column('continent_2');
        // CRUD::column('region_1');
        // CRUD::column('region_2');
        // CRUD::column('country_1');
        // CRUD::column('country_2');
        // CRUD::column('country_3');
        // CRUD::column('country_4');

        CRUD::column('validation_result');
    }


    public function getImportForm()
    {
        ray('TempProjectCrudController.getImportForm()...');

        $this->crud->hasAccessOrFail('import');
        $this->crud->setOperation('import');

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();

        // TODO:
        // 1. change entity name from "Temp Projects" to "Initiatives" after testing
        // 2. hide "<< Back to all temp projects" link
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
        ray('TempProjectCrudController.postImportForm()...');

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


        // call Laravel Excel package to import excel file
        // Questions:
        // 1. How to set the validation request class?
        // 2. portfolio is passed as a parameter, how will it be used?

        // pass portfolio model to importer, which is specified in setup()
        // Excel::import(new $importer($portfolio), $request->importFile);

        // TODO:
        // 1. call Laravel Excel package to read excel file, return it as a collection
        // 2. handle rows one by one, use validation request class to validate data and get validation result
        // 3. create TempProject model with validation result for each row
        // 4. redirect to TempProjectCrudController list view to show uploaded data with validation result

        Alert::success(trans('backpack::crud.insert_success'))->flash();

        // redirect to import redirect route if specified
        if ($route = $this->crud->get('import.redirect')) {
            return redirect(url($route));
        }

        // redirect to list view
        return redirect(url($this->crud->route));
    }
}
