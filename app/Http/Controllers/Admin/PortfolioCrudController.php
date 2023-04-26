<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;
use App\Http\Requests\PortfolioRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class PortfolioCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PortfolioCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }

    use AuthorizesRequests;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        if ( !Session::exists('selectedOrganisationId') ) {
            throw new BadRequestHttpException('Please select an institution first');
        }

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

        $selectedOrganisationId = Session::get('selectedOrganisationId');
        $this->crud->addClause('where', 'organisation_id', $selectedOrganisationId);
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

        $selectedOrganisationId = Session::get('selectedOrganisationId');
        CRUD::field('organisation_id')->type('hidden')->default($selectedOrganisationId);

        CRUD::field('name');
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
        $this->authorize('delete', Portfolio::find($id));

        $this->crud->hasAccessOrFail('delete');
    
        return $this->crud->delete($id);
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    public function show($id)
    {
        $this->authorize('view', Portfolio::find($id));

        $content = $this->traitShow($id);

        return $content;
    }

}
