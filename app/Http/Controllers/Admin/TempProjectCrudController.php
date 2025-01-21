<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;
use App\Models\TempProject;
use App\Models\TempProjectImport;
use Prologue\Alerts\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use Backpack\CRUD\app\Library\Widget;
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

    use ShowOperation {
        show as traitShow;
    }

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

        // TODO: show entity name as temp initiative temporary for testing
        CRUD::setEntityNameStrings('temp initiative', 'temp initiatives');
        // CRUD::setEntityNameStrings('initiative', 'initiatives');

        // specify importer class
        CRUD::set('import.importer', TempProjectWorkbookImport::class);

        // add import template file and template file filename
        CRUD::set('import.template', InitiativeImportTemplateExportWorkbook::class);
        CRUD::set('import.template-name', 'Agroecology Funding Tool - Initiative Import Template.xlsx');
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

        // add import button
        $this->crud->addButton('top', 'import', 'view', 'vendor.backpack.crud.buttons.import', 'end');

        // TODO: add finalise button, only enable it if there is no validation error
    }

    public function getImportForm()
    {
        $this->crud->hasAccessOrFail('import');
        $this->crud->setOperation('import');

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();

        // TODO:
        // Question: how to hide "<< Back to all temp initiatives" link in Import page?
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

        // form validation, to make sure user has selected a portfolio and excel file
        $request = $this->crud->validateRequest();

        Validator::make($request->all(), [
            'portfolio' => 'required',
            'importFile' => 'required',
        ])->validate();


        // find portfolio model
        $portfolio = Portfolio::find($request->portfolio);

        // create or get ProjectImport model belongs to logged in user
        $tempProjectImport = TempProjectImport::firstOrCreate([
            'user_id' => auth()->user()->id,
        ]);

        // remove all temp_projects records related to this user
        TempProject::where('temp_project_import_id', $tempProjectImport->id)->delete();

        // call Laravel Excel package to import excel file into temp_projects table
        Excel::import(new $importer($portfolio, $tempProjectImport), $request->importFile);

        // remove the previously attached excel file
        $tempProjectImport->clearMediaCollection('project_import_excel_file');

        // attach the uploaded excel file to TempProjectImport model
        $tempProjectImport->addMediaFromRequest('importFile')->toMediaCollection('project_import_excel_file');

        // find number of invalid records
        $numberOfInvalidRecords = TempProject::where('temp_project_import_id', $tempProjectImport->id)->where('valid', 0)->count();

        // check if the uploaded file contains no error, then it is ready to import (enable Finalise button)
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


    public function show($id)
    {
        // custom logic before
        $content = $this->traitShow($id);

        // custom logic after
        return $content;
    }

    protected function setupShowOperation()
    {
        // add a widget to show validation result with color and multiple lines
        if ($this->crud->getCurrentEntry()->valid) {

            Widget::add()->to('before_content')->type('div')->class('row')->content([

                Widget::make(
                    [
                        'type'    => 'card',
                        'class'   => 'card bg-primary text-white',
                        'wrapper' => ['class' => 'col-sm-4 col-md-8'],
                        'content'    => [
                            'header' => 'Validation result',
                            'body'   => 'The initiative data is correct.',
                        ]
                    ]
                ),
            ]);
        } else {

            Widget::add()->to('before_content')->type('div')->class('row')->content([

                Widget::make(
                    [
                        'type'    => 'card',
                        'class'   => 'card bg-error text-white',
                        'wrapper' => ['class' => 'col-sm-4 col-md-8'],
                        'content'    => [
                            'header' => 'Validation result',
                            'body'   => $this->crud->getCurrentEntry()->validation_result,
                        ]
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
        CRUD::column('continent_1');
        CRUD::column('continent_2');
        CRUD::column('region_1');
        CRUD::column('region_2');
        CRUD::column('country_1');
        CRUD::column('country_2');
        CRUD::column('country_3');
        CRUD::column('country_4');
    }
}
