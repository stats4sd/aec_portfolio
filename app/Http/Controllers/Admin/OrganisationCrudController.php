<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organisation;
use App\Http\Requests\OrganisationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;


class OrganisationCrudController extends AdminPanelCrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation { destroy as traitDestroy; }

    use AuthorizesRequests;

    public function setup()
    {
        CRUD::setModel(\App\Models\Organisation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/organisation-crud');
        CRUD::setEntityNameStrings('institution', 'institutions');

        CRUD::denyAccess('delete');

        parent::setup();
    }

    protected function setupListOperation()
    {

        $this->authorize('viewAny', Organisation::class);

        CRUD::setResponsiveTable(false);
        CRUD::column('name');
        CRUD::column('institutionType')->label('Institution Type');
        CRUD::column('fundingFlowAnalysis')->label('Contribute to Funding Flow Analysis');
        CRUD::column('projects')->type('relationship_count');
    }

    protected function setupCreateOperation()
    {
        if ($this->crud->getCurrentOperation() === 'Create') {
            $this->authorize('create', Organisation::class);
        }

        CRUD::field('name')->label('Enter the Institution name')->validationRules('required');
        CRUD::field('currency')->label('Enter the Institution\'s default currency')
        ->hint('This currency will be used for the summary dashboard. All initiative budgets for your institution will be converted into this currency');
        CRUD::field('contributes_to_funding_flow')->label('The institution will contribute to the funding flow analysis');
        CRUD::field('institutionType')->label('Select the type of institution.');
        CRUD::field('institution_type_other')->label('Enter the "other" type of institution');
    }

    protected function setupUpdateOperation()
    {

        $this->authorize('update', CRUD::getCurrentEntry());

        $this->setupCreateOperation();
    }

    public function destroy($id)
    {
        $this->authorize('delete', Organisation::find($id));

        $this->crud->hasAccessOrFail('delete');

        return $this->crud->delete($id);
    }

    public function show($id)
    {
        $organisation = Organisation::find($id);

        $this->authorize('view', $organisation);

        return view('organisations.show', ['organisation' => $organisation]);
    }

}
