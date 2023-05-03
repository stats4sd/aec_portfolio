<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Enums\AssessmentStatus;
use App\Models\Principle;
use App\Models\PrincipleProject;
use App\Models\Project;
use App\Models\RedLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

trait RedlineOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRedlineRoutes($segment, $routeName, $controller)
    {
        logger("RedlineOperation.setupRedlineRoutes()");

        Route::get($segment . '/{id}/redline', [
            'as' => $routeName . '.redline',
            'uses' => $controller . '@redline',
            'operation' => 'redline',
        ]);

        Route::put($segment . '/{id}/redline', [
            'as' => $routeName . '.postRedline',
            'uses' => $controller . '@postRedlineForm',
            'operation' => 'redline',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRedlineDefaults()
    {
        logger("RedlineOperation.setupRedlineDefaults()");

        $this->crud->allowAccess('redline');

        $this->crud->operation('redline', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'assess', 'view', 'crud::buttons.assess');
            $this->crud->addButton('line', 'redline', 'view', 'crud::buttons.redline')->makeFirst();
        });

        $this->crud->setupDefaultSaveActions();

    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function redline($id)
    {
        logger("RedlineOperation.redline()");

        $this->crud->hasAccessOrFail('redline');

        $id = $this->crud->getCurrentEntryId() ?? $id;

        dump($id);

        // TODO: it is setting the project record to "entry", corresponind project_red_line records will be showed
        // Question: how to change to set the latest assessment record to "entry", get corresponding project_red_line records via assessment_id?
        $this->data['entry'] = $this->crud->getEntryWithLocale($id);
        // $this->data['entry'] = $this->crud->getEntryWithLocale($id)->assessments->last();

        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();

        $this->data['id'] = $id;

        // load the view
        return view("crud::operations.redline", $this->data);
    }

    public function postRedlineForm(Request $request)
    {
        logger("RedlineOperation.postRedlineForm()");

        if($request->has('redlines_compelete') && $request->redlines_complete === 1) {
            foreach(RedLine::all() as $redline) {
                if($request->has('redline_value_' . $redline->id)) {

                    if($request->{'redline_value'.$redline->id} === null) {
                        $field = 'redline_value_' . $redline->id;
                        throw ValidationException::withMessages([
                            $field => 'You must select an option for every Redline before marking the assessment as complete',
                        ]);
                    }
                }
            }
        }

        $updates = [];
        // custom handling of redline-project relationship data
        foreach (Redline::all() as $redline) {
            if ($request->has('redline_value_' . $redline->id)) {
                $updates[$redline->id] = ['value' => $request->{'redline_value_' . $redline->id}];
            }
        }

        // DONE - TODO: sync red line records via the latest assessment record instead of project record
        $project = Project::findOrFail($request->id);
        // $project->redlines()->sync($updates);
        $latestAssessment = $project->assessments->last();
        $latestAssessment->redlines()->sync($updates);

        // TODO: update assessment status via the latest assessment record instead of project record
        // if the main assessment is already in progress, only update the status if the redlines are 'no longer' complete:        

        // if (collect(AssessmentStatus::InProgress, AssessmentStatus::Complete)->contains($project->assessment_status)) {
        //     $project->assessment_status = !$request->redlines_complete ? AssessmentStatus::RedlinesIncomplete : $project->assessment_status;
        // } else {
        //     // if a redline fails, the assessment is complete!
        //     if($project->failingRedlines()->count() > 0) {
        //         $project->assessment_status = AssessmentStatus::Complete;
        //     } else {
        //         $project->assessment_status = $request->redlines_complete ? AssessmentStatus::RedlinesComplete : AssessmentStatus::RedlinesIncomplete;
        //     }
        // }

        if (collect(AssessmentStatus::InProgress, AssessmentStatus::Complete)->contains($latestAssessment->assessment_status)) {
            $latestAssessment->assessment_status = !$request->redlines_complete ? AssessmentStatus::RedlinesIncomplete : $latestAssessment->assessment_status;
        } else {
            // if a redline fails, the assessment is complete!
            if($latestAssessment->failingRedlines()->count() > 0) {
                $latestAssessment->assessment_status = AssessmentStatus::Complete;
            } else {
                $latestAssessment->assessment_status = $request->redlines_complete ? AssessmentStatus::RedlinesComplete : AssessmentStatus::RedlinesIncomplete;
            }
        }

        // $project->save();
        $latestAssessment->save();


        return redirect($this->crud->route);
    }
}
