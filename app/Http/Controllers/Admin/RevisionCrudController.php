<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
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
        // do not remember user's filtering, search and pagination when user leave the page.
        // it is to avoid keeping user selected initiative of institution A in filter after user changing to institution B
        CRUD::disablePersistentTable();

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

        // add filter by item type
        $this->crud->addFilter([
            'name' => 'item_type',
            'type' => 'dropdown',
            'label' => 'Filter by Item Type'
        ], [
            0 => 'Red Flag',
            1 => 'Principle',
            2 => 'Additional Criteria',
        ], function ($value) {
            if ($value == 0) {
                $this->crud->addClause('where', 'revisionable_type', 'App\Models\AssessmentRedLine');
            } else if ($value == 1) {
                $this->crud->addClause('where', 'revisionable_type', 'App\Models\PrincipleAssessment');
            } else if ($value == 2) {
                $this->crud->addClause('where', 'revisionable_type', 'App\Models\AdditionalCriteriaAssessment');
            }
        });

    }

}
