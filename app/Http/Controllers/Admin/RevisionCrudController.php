<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RevisionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RevisionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Revision::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/revision');
        CRUD::setEntityNameStrings('revision', 'revisions');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::disablePersistentTable();

        // Question: How to show revisions records belong to initiatives that belong to user selected organisation?
        //
        // In revisions table, there is no column to indicate which organisation it belongs to.
        //
        // We can only find out the corresponding organisation very indirectly,
        // i.e. find Red Flag / Principle / Additional Criteria model => Assessment model => Project model => Organisation ID
        //
        // It seems that "CRUD::addClause()" cannot be used here as it can only specify condition for table columns directly.
        //
        //
        // Possible workaround:
        //
        // 1. Based on user selected organisation id, find out all related records of red flag / principle / additional criteria,
        // only show revisions record with matched revisionable_type and revisionable_id.
        //
        // It sounds complicated, and we may have performance issue when we have more and more records.
        //
        // 2. Do not show any revision records at the beginning. User must select an initiative then only show related revision
        // records in list view. As the filter only shows initiatives belong to user selected organisation, the revisions records
        // to be showed must belong to user selected organisation.
        //
        // This approach is simpler, and it is unlikely to have performance issue.
        // But we may need to provide additioanl instructions on how to use this CRUD panel.


        // add a clause that no revisions record will meet, it will return an empty set
        // therefore no revisions record showed at the beginning
        //CRUD::addClause('where', 'id', '=', 0);


        CRUD::column('project.name')->label('Initiative');
        CRUD::column('item_type');
        CRUD::column('item');
        CRUD::column('key');
        CRUD::column('old_value');
        CRUD::column('new_value');

        $this->crud->addColumns([
            [
                'name' => 'user_id',
                'label' => 'User',
                'type' => 'select',
                'entity' => 'user',
                'attribute' => 'name',
                'model' => User::class,
                'wrapper' => [
                    'element' => 'div',
                    'width' => "200px",
                ],
            ],
        ]);

        CRUD::column('created_at');


        // add filter by initiative
        $this->crud->addFilter(
            [
                'type' => 'select2',
                'name' => 'projects',
                'label' => 'Filter by Initiative',
            ],
            function () {
                return Project::where('organisation_id', Session::get('selectedOrganisationId'))->get()->pluck('name', 'id')->toArray();
            },
            function ($value) {
                $this->crud->query->whereHas('revisionable', function($query) use ($value) {
                    $query->whereHas('assessment', function($query) use ($value) {
                        $query->where('project_id', $value);
                    });
                });
            }
        );

        // Maybe it is useful to add filter by item type here.
        // But it may be complicated when user filter by item type without filtering by initiative.
        // Consider this feature as a nice to have feature or future enhancement.

    }

}
