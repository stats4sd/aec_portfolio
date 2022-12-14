<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Requests\ImportRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Prologue\Alerts\Facades\Alert;

/**
 * This trait is for Import feature
 */
trait ImportOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
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

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
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

        return view('file-util::vendor.backpack.crud.import::import', $this->data);
    }

    /**
     * Import All rows to Excel
     *
     * @return \Illuminate\View\View
     */
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
        if (!$this->crud->get('import.template-path')) {
            abort(500, 'No import template provided');
        }
        return Storage::download($this->crud->get('import.template-path'));
    }
}
