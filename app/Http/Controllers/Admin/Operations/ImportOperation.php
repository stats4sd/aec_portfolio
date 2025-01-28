<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Helpers\OrganisationSelector;
use App\Http\Requests\ImportRequest;
use App\Models\Organisation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Prologue\Alerts\Facades\Alert;

/**
 * This trait is for Import feature
 */
trait ImportOperation
{

    protected function setupImportRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/import', [
            'as' => $routeName . '.getImport',
            'uses' => $controller . '@getImportForm',
            'operation' => 'import',
        ]);

        Route::post($segment . '/import', [
            'as' => $routeName . '.postImport',
            'uses' => $controller . '@postImportForm',
            'operation' => 'import',
        ]);

        Route::get($segment . '/import-template', [
            'as' => $routeName . 'getImportTemplate',
            'uses' => $controller . '@getImportTemplate',
            'operation' => 'import',
        ]);
    }

    protected function setupImportDefaults()
    {
        $this->crud->allowAccess('import');
        $this->crud->operation('import', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            $this->crud->setupDefaultSaveActions();
        });

        // Inline Create Operation spams the 'setupCreteOperation' fields+validation within the setupInlineCreateDefaults(), pushing them into all operations;
        $this->crud->operation('import', function () {
            $this->crud->removeAllFields();
            $this->crud->setValidation(ImportRequest::class);
        });


        $this->crud->operation('list', function () {
            $this->crud->addButton('top', 'import', 'view', 'file-util::vendor.backpack.crud.buttons.import');

            // custom to this project
            if ($this->crud->get('import.template-path')) {
                $this->crud->addButton('top', 'import-template', 'view', 'vendor.backpack.crud.buttons.import-template');
            }
        });
    }

    public function getImportForm()
    {
        $this->crud->hasAccessOrFail('import');
        $this->crud->setOperation('import');

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();

        $this->data['title'] = 'Import ' . $this->crud->entity_name . ' from excel file';

        $this->crud->addField([
            'name' => 'importFile',
            'type' => 'upload',
            'label' => 'Select Excel File to Upload',
        ]);

        return view('backpack::crud.operations.import', $this->data);
    }


    public function postImportForm()
    {
        $this->crud->hasAccessOrFail('import');
        $importer = $this->crud->get('import.importer');

        if (!$importer) {
            return response("Importer Class not found - please check the importer is properly setup for this page", 500);
        }

        $request = $this->crud->validateRequest();

        Excel::import(new $importer(), $request->importFile);


        Alert::success(trans('backpack::crud.insert_success'))->flash();

        if ($route = $this->crud->get('import.redirect')) {
            return redirect(url($route));
        }

        return redirect(url($this->crud->route));
    }

    public function getImportTemplate()
    {

        $importTemplate = $this->crud->get('import.template');
        $importTemplateName = $this->crud->get('import.template-name');

        return Excel::download(new $importTemplate(OrganisationSelector::getSelectedOrganisation()), $importTemplateName);
    }
}
