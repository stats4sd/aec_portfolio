<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organisation;
use App\Models\OrganisationMember;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PortfolioRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        if ( !auth()->user()->can('view portfolios') ) {
            throw new AccessDeniedHttpException('Access denied. You do not have permission to access this page');
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
        CRUD::column('organisation_id');
        CRUD::column('name');

        // find the organisations that user belongs to
        $userOrganisationIds = OrganisationMember::select('organisation_id')->where('user_id', Auth::user()->id)->get();

        // site admin and site manager do not belong to any organisation
        // institutional admin, assessor, member belong to at least one organisation
        if ( count($userOrganisationIds) > 0 ) {
            // show all portfolios to site admin and site manager
            // otherwise, show portfolios belong to user's organisations
            $this->crud->addClause('whereIn', 'organisation_id', $userOrganisationIds);
        }

        // add filter for site admin, site manager, or user belong to more than one organisation
        if ( count($userOrganisationIds) != 1 ) {
            // filter by organisation
            CRUD::filter('organisation_id')
                ->type('select2')
                ->label('Filter by Organisation')
                ->values(function () {
                    return Organisation::get()->pluck('name', 'id')->toArray();
                })
                ->whenActive(function ($value) {
                    CRUD::addClause('where', 'organisation_id', $value);
                });
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
        CRUD::setValidation(PortfolioRequest::class);

        CRUD::field('organisation_id');
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
        $this->setupCreateOperation();
    }
}
