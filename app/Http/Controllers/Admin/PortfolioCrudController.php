<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GeographicalReach;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Http\Requests\PortfolioRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Prologue\Alerts\Facades\Alert;


class PortfolioCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation {
        destroy as traitDestroy;
    }
    use ShowOperation {
        show as traitShow;
    }

    use AuthorizesRequests;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {

        CRUD::setModel(\App\Models\Portfolio::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/portfolio');
        CRUD::setEntityNameStrings('portfolio', 'portfolios');
    }


    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->authorize('viewAny', Portfolio::class);

        CRUD::column('organisation_id');
        CRUD::column('name');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->authorize('create', Portfolio::class);

        CRUD::setValidation(PortfolioRequest::class);

        $selectedOrganisation = Organisation::find(Session::get('selectedOrganisationId'));

        CRUD::field('organisation_id')->type('hidden')->default($selectedOrganisation->id);

        CRUD::field('name')->label('Enter the name of the portfolio');


        CRUD::field('currency-info')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Currency and Budget')
            ->content("$selectedOrganisation->name uses $selectedOrganisation->currency as the default currency. When you create initiatives, you will be given the option to choose any currency for the initiative budget, and to find or enter the correct exchange rate. For portfolios, please enter the total budget using the institution's default currency of $selectedOrganisation->currency");

        CRUD::field('budget')
            ->prefix($selectedOrganisation->currency)
            ->hint('Enter the overall budget for the portfolio');

        CRUD::field('geographic_reach')
            ->type('select2_from_array')
            ->options(Arr::mapWithKeys(GeographicalReach::cases(), fn($enum) => [$enum->value => $enum->value]));


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
    }

    /**
     * Define what happens when the Delete operation is loaded.
     */
    public function destroy($id)
    {
        $portfolio = Portfolio::find($id);

        $this->authorize('delete', $portfolio);

        $this->crud->hasAccessOrFail('delete');

        $portfolio->delete();

        Alert::add('success', "$portfolio->name was successfully deleted")->flash();
      
        return back();
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    public function show($id)
    {
        $this->authorize('view', Portfolio::find($id));

        return view('organisations.portfolio');
    }

}
