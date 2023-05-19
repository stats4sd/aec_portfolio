<?php

namespace App\Http\Controllers\Admin\Operations;

use Carbon\Carbon;
use App\Models\Principle;
use Illuminate\Http\Request;
use App\Enums\AssessmentStatus;
use App\Models\PrincipleAssessment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait AssessOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupAssessRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/assess', [
            'as' => $routeName . '.assess',
            'uses' => $controller . '@assess',
            'operation' => 'assess',
        ]);

        Route::put($segment . '/{id}/assess', [
            'as' => $routeName . '.postAssess',
            'uses' => $controller . '@postAssessForm',
            'operation' => 'assess',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupAssessDefaults()
    {
        $this->crud->allowAccess('assess');

        $this->crud->operation('assess', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButton('line', 'assess', 'view', 'crud::buttons.assess')->makeFirst();
        });

        $this->crud->setupDefaultSaveActions();

    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function assess($id)
    {
        // logger("AssessOperation.assess()");

        $this->crud->hasAccessOrFail('assess');
        // $this->crud->setOperation('Assess');

        $id = $this->crud->getCurrentEntryId() ?? $id;

        // dump($id);

        // TODO: it is setting the project record to "entry", corresponind project_red_line records will be showed
        // Question: how to change to set the latest assessment record to "entry", get corresponding project_red_line records via assessment_id?
        // maybe related to issue: "custom_score_tags records of current assessment will be deleted after saving assessment every time"
        $this->data['entry'] = $this->crud->getEntryWithLocale($id);

        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = 'Assess ' . $this->crud->entity_name;
        $this->data['heading'] = 'Assess AE Principles for ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        // load the view
        return view("crud::operations.assess", $this->data);
    }

    public function postAssessForm(Request $request)
    {
        // logger("AssessOperation.postAssessForm()");

        $project = $this->crud->getEntry($request->input('id'));

        // DONE - TODO: delete all custom score tags for latest asseessment
        // delete all custom score tags for the project
        // $project->customScoreTags()->delete();
        $latestAssessment = $project->assessments->last();
        $latestAssessment->customScoreTags()->delete();

        // validate fields
        $rules = [];

        foreach (Principle::all() as $principle) {
            $rules[$principle->id . "_rating"] = ['nullable', 'numeric', 'max:2', 'min:0'];
        }

        // TODO: figure out why validator redirects and then _current_tab is reset to 1st tab, even though a different tab is shown...
        Validator::make($request->input(), $rules, [
            'max' => 'Ratings must be between 0 and 2',
            'min' => 'Ratings must be between 0 and 2',
            'numeric' => 'Ratings cannot be text - they must be a number',
        ])->validate();


        foreach (Principle::all() as $principle) {
            $principleId = $principle->id;

            // DONE - TODO: get principles from latest assessment instead of project
            // $project->principles()->updateExistingPivot($principleId, [
            //     'rating' => $request->input("${principleId}_rating"),
            //     'rating_comment' => $request->input("${principleId}_rating_comment"),
            //     'is_na' => $request->input("${principleId}_is_na") ?? 0,
            // ]);

            $latestAssessment->principles()->updateExistingPivot($principleId, [
                'rating' => $request->input("${principleId}_rating"),
                'rating_comment' => $request->input("${principleId}_rating_comment"),
                'is_na' => $request->input("${principleId}_is_na") ?? 0,
            ]);


            // DONE - TODO: get principle_project record by latest assessment instead of project
            // $principleProject = PrincipleProject::where('project_id', $project->id)->where('principle_id', $principleId)->first();
            // $principleProject = PrincipleProject::where('assessment_id', $latestAssessment->id)->where('principle_id', $principleId)->first();
            $principleAssessment = PrincipleAssessment::where('assessment_id', $latestAssessment->id)->where('principle_id', $principleId)->first();

            $sync = json_decode($request->input("scoreTags" . $principle->id));
            $syncPivot = [];

            if ($sync) {

                for ($i = 0, $iMax = count($sync); $i < $iMax; $i++) {
                    // DONE - TODO: store latest assessment ID into custom_score_tags.assessment_id
                    // $syncPivot[] = ['project_id' => $project->id];
                    $syncPivot[] = ['assessment_id' => $latestAssessment->id];

                    // TODO: Column principle_project_score_tag.principle_project_id will be changed to principle_project_score_tag.principle_assessmnent_id
                    // Check whether principle_project_score_tag.principle_assessmnent_id will be filled with value of principle_project_id after changing column name
                }

                $sync = collect($sync)->combine($syncPivot);
                $principleAssessment->scoreTags()->sync($sync->toArray());
            }

            $custom_score_tags = json_decode($request->input("customScoreTags" . $principle->id), true);

            if ($custom_score_tags) {

                for ($i = 0, $iMax = count($custom_score_tags); $i < $iMax; $i++) {

                    if (empty($custom_score_tags[$i])){
                        unset($custom_score_tags[$i]);
                    }

                    elseif (!array_key_exists('name', $custom_score_tags[$i])) {
                        throw ValidationException::withMessages(['customScoreTags' . $principle->id => 'New examples/indicators must have a name']);
                    }

                    else {
                        // $custom_score_tags[$i]['project_id'] = $project->id;
                        
                        // DONE - TODO: store latest assessment ID into custom_score_tags.assessment_id
                        $custom_score_tags[$i]['assessment_id'] = $latestAssessment->id;
                    }

                }

                // $principleProject->customScoreTags()->createMany($custom_score_tags);
                $principleAssessment->customScoreTags()->createMany($custom_score_tags);
            }

        }

        // DONE - TODO: save assessment_status in assessment table instead of project table
        // $project->assessment_status = $request->assessment_complete ? AssessmentStatus::Complete : AssessmentStatus::InProgress;
        // $project->save();

        if ($request->assessment_complete) {
            $latestAssessment->assessment_status = AssessmentStatus::Complete;
            $latestAssessment->completed_at = Carbon::now()->format('Y-m-d');
        } else {
            $latestAssessment->assessment_status = AssessmentStatus::InProgress;
        }
        
        $latestAssessment->save();


        return $this->crud->performSaveAction();
    }
}
