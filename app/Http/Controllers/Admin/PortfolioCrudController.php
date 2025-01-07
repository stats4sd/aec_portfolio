<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Organisation;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\PortfolioRequest;
use Illuminate\Support\Facades\Session;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;


class PortfolioCrudController extends CrudController
{
    use ListOperation;

    use CreateOperation {
        store as traitStore;
    }

    use UpdateOperation {
        update as traitUpdate;
    }

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
        CRUD::removeSaveActions(['save_and_edit', 'save_and_new', 'save_and_preview']);

        $selectedOrganisation = Organisation::find(Session::get('selectedOrganisationId'));

        CRUD::field('organisation_id')->type('hidden')->default($selectedOrganisation->id);

        CRUD::field('name')->label('Enter the name of the portfolio');

        CRUD::field('description')->label('Describe the portfolio')->hint('Please include key information about the portfolio, such as the key aims, geographic scope or beneficiary targets');

        CRUD::field('currency-info')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Currency and Budget')
            ->content("$selectedOrganisation->name uses $selectedOrganisation->currency as the default currency. When you create initiatives, you will be given the option to choose any currency for the initiative budget, and to find or enter the correct exchange rate. For portfolios, please enter the total budget using the institution's default currency of $selectedOrganisation->currency");

        // change budget field to a hidden field
        CRUD::field('budget')
            ->type('hidden')
            ->prefix($selectedOrganisation->currency)
            ->hint('Enter the overall budget for the portfolio');

        // use displayBudget to accept budget with thousand separators
        CRUD::field('displayBudget')
            ->label('Budget')
            ->prefix($selectedOrganisation->currency)
            ->hint('Enter the overall budget for the portfolio');

        CRUD::field('funding-flow-info')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title('Funding Flow Analysis')
            ->content('Please indicate whether this portfolio should be included in the Funding Flow Analysis conducted by the Agroecology Coalition. If this portfolio contains real projects (past or present) that are funded by or through your institution (even if the funds originated elsewhere), please tick this box. If you are using this portfolio to test the system, to enter projects that are being planned, or you are a partner organisation that is not directly funding the projects, please leave this box unticked. If you are unsure, please contact the Agroecology Coalition for guidance.');
        CRUD::field('contributes_to_funding_flow')->label('Contributes to Funding Flow Analysis');
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

    public function store()
    {
        $this->calculateBudget();

        return $this->traitStore();
    }

    public function update()
    {
        $this->calculateBudget();

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
