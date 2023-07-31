<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use App\Models\Revision;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RevisionRequest;
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
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

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
        CRUD::column('project.organisation.id');

        CRUD::column('project.name')->label('Project');

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


        // TODO: add filter silently to show projects of selected organisation only?

        // add filter for project
        $this->crud->addFilter(
            [
                'type' => 'select2',
                'name' => 'projects',
                'label' => 'filter by project',
            ],
            function () {
                return Project::where('organisation_id', Session::get('selectedOrganisationId'))->get()->pluck('name', 'id')->toArray();
            },
            function ($values) {
                logger('Selected project id: ' . $values);
                
                // find assessment red line ids belong to related assessments
                $assessmentRedLineIds = DB::table("assessment_red_line")->select('id')->whereIn('assessment_id', function ($query) use ($values) {
                    // find assessment_id belongs to selected project
                    $query->select('id')->from('assessments')->where('project_id', $values);
                })
                ->pluck('id');

                // find principle assessment ids belong to related assessments
                $principleAssessmentIds = DB::table("principle_assessment")->select('id')->whereIn('assessment_id', function ($query) use ($values) {
                    // find assessment_id belongs to selected project
                    $query->select('id')->from('assessments')->where('project_id', $values);
                })
                ->pluck('id');

                // find additional criteria assessment ids belong to related assessments
                $additionalCriteriaAssessmentIds = DB::table("additional_criteria_assessment")->select('id')->whereIn('assessment_id', function ($query) use ($values) {
                    // find assessment_id belongs to selected project
                    $query->select('id')->from('assessments')->where('project_id', $values);
                })
                ->pluck('id');

                // dump($assessmentRedLineIds);
                // dump($principleAssessmentIds);
                // dump($additionalCriteriaAssessmentIds);

                $firstQuery = Revision::where('revisionable_type', 'App\Models\AssessmentRedLine')->whereIn("revisionable_id", $assessmentRedLineIds);

                $secondQuery = Revision::where('revisionable_type', 'App\Models\PrincipleAssessment')->whereIn("revisionable_id", $principleAssessmentIds);

                $thirdQuery = Revision::where('revisionable_type', 'App\Models\AdditionalCriteriaAssessment')->whereIn("revisionable_id", $additionalCriteriaAssessmentIds);

                // TODO: union 3 query results together
                $this->crud->query->where('revisionable_type', 'App\Models\AssessmentRedLine')->whereIn("revisionable_id", $assessmentRedLineIds);

                // TODO
                // $this->crud->query = $secondQuery->union($thirdQuery);

            }
        );
    }

}
