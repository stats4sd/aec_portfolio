<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\AdditionalCriteriaAssessment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\AssessmentStatus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait AssessCustomOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupAssessCustomRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/assess-custom', [
            'as' => $routeName . '.assessCustom',
            'uses' => $controller . '@assessCustom',
            'operation' => 'assessCustom',
        ]);

        Route::put($segment . '/{id}/assess-custom', [
            'as' => $routeName . '.postAssessCustom',
            'uses' => $controller . '@postAssessCustomForm',
            'operation' => 'assessCustom',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupAssessCustomDefaults()
    {
        $this->crud->allowAccess('assessCustom');

        $this->crud->operation('assessCustom', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->setupDefaultSaveActions();

    }

    /**
     * Show the view for performing the operation.
     *
     */
    public function assessCustom($id)
    {
        $this->crud->hasAccessOrFail('assessCustom');
        // $this->crud->setOperation('Assess');

        $id = $this->crud->getCurrentEntryId() ?? $id;


        $this->data['entry'] = $this->crud->getEntryWithLocale($id);

        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = 'Assess ' . $this->crud->entity_name;
        $this->data['heading'] = 'Assess AE Principles for ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        // load the view
        return view("crud::operations.assess-custom", $this->data);
    }

    public function postAssessCustomForm(Request $request)
    {

        $latestAssessment = $this->crud->getEntry($request->input('id'));

        $institution = $latestAssessment->project->organisation;

        // validate fields
        $rules = [];

        foreach ($institution->additionalCriteria as $assessmentCriterion) {
            $rules[$assessmentCriterion->id . "_rating"] = ['nullable', 'numeric', 'max:2', 'min:0'];
        }

        // TODO: figure out why validator redirects and then _current_tab is reset to 1st tab, even though a different tab is shown...
        Validator::make($request->input(), $rules, [
            'max' => 'Ratings must be between 0 and 2',
            'min' => 'Ratings must be between 0 and 2',
            'numeric' => 'Ratings cannot be text - they must be a number',
        ])->validate();


        foreach ($institution->additionalCriteria as $assessmentCriterion) {
            $assessmentCriterionId = $assessmentCriterion->id;

            $criteriaAssessment = AdditionalCriteriaAssessment::where('assessment_id', $latestAssessment->id)->where('additional_criteria_id', $assessmentCriterionId)->first();

            $criteriaAssessment->rating = $request->input("${assessmentCriterionId}_rating");
            $criteriaAssessment->rating_comment = $request->input("${assessmentCriterionId}_rating_comment");
            $criteriaAssessment->is_na = $request->input("${assessmentCriterionId}_is_na") ?? 0;

            $criteriaAssessment->save();

            $sync = json_decode($request->input("additionalCriteriaScoreTags" . $assessmentCriterionId));
            $syncPivot = [];

            if ($sync) {

                for ($i = 0, $iMax = count($sync); $i < $iMax; $i++) {
                    $syncPivot[] = ['assessment_id' => $latestAssessment->id];
                }

                $sync = collect($sync)->combine($syncPivot);


                $criteriaAssessment->additionalCriteriaScoreTags()->sync($sync->toArray());
            }

            $custom_score_tags = json_decode($request->input("customScoreTags" . $assessmentCriterionId), true);

            if ($custom_score_tags) {

                for ($i = 0, $iMax = count($custom_score_tags); $i < $iMax; $i++) {

                    if (empty($custom_score_tags[$i])) {
                        unset($custom_score_tags[$i]);
                    } elseif (!array_key_exists('name', $custom_score_tags[$i])) {
                        throw ValidationException::withMessages(['customScoreTags' . $assessmentCriterion->id => 'New examples/indicators must have a name']);
                    } else {
                        $custom_score_tags[$i]['assessment_id'] = $latestAssessment->id;
                    }

                }

                // purge and recreate all custom score tags (easier than checking to see if we should delete individual ones)
                $criteriaAssessment->additionalCriteriaCustomScoreTags()->delete();
                $criteriaAssessment->additionalCriteriaCustomScoreTags()->createMany($custom_score_tags);
            }
        }


        if ($request->assessment_complete) {
            $latestAssessment->additional_status = AssessmentStatus::Complete;
            $latestAssessment->completed_at = Carbon::now()->format('Y-m-d');
        } else {
            $latestAssessment->additional_status = AssessmentStatus::InProgress;
        }

        $latestAssessment->save();


        return $this->crud->performSaveAction();
    }
}
