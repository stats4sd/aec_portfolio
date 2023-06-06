<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\AssessmentRedLine;
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
        // logger("RedlineOperation.setupRedlineRoutes()");

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
     */
    public function redline($id)
    {
        $this->crud->hasAccessOrFail('redline');

        $id = $this->crud->getCurrentEntryId() ?? $id;

        $this->data['entry'] = $this->crud->getEntryWithLocale($id);

        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();

        $this->data['id'] = $id;

        // load the view
        return view("crud::operations.redline", $this->data);
    }

    public function postRedlineForm(Request $request)
    {
        if ($request->has('redlines_compelete') && $request->redlines_complete === 1) {
            foreach (RedLine::all() as $redline) {
                if ($request->has('redline_value_' . $redline->id)) {

                    if ($request->{'redline_value' . $redline->id} === null) {
                        $field = 'redline_value_' . $redline->id;
                        throw ValidationException::withMessages([
                            $field => 'You must select an option for every Redline before marking the assessment as complete',
                        ]);
                    }
                }
            }
        }

        $latestAssessment = Assessment::findOrFail($request->id);
        $updates = [];


        // custom handling of redline-project relationship data
        foreach (Redline::all() as $redline) {
            if ($request->has('redline_value_' . $redline->id)) {

                // Need to call save() on the AssessmentRedline model directly in order to save the update via the Revisionable trait:
                $assessmentRedLine = AssessmentRedLine::where('assessment_id', $latestAssessment->id)
                    ->where('red_line_id', $redline->id)
                    ->first();

                $assessmentRedLine->value = $request->{'redline_value_' . $redline->id};
                $assessmentRedLine->save();

            }
        }

        // set redline status
        $latestAssessment->redline_status = $request->redlines_complete ? AssessmentStatus::Complete : AssessmentStatus::InProgress;
        $latestAssessment->save();


        return redirect(backpack_url('project'));
    }
}
