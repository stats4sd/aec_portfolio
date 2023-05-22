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
        $this->crud->hasAccessOrFail('assess');
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
        return view("crud::operations.assess", $this->data);
    }

    public function postAssessCustomForm(Request $request)
    {

        $latestAssessment = $this->crud->getEntry($request->input('id'));
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

            $latestAssessment->principles()->updateExistingPivot($principleId, [
                'rating' => $request->input("${principleId}_rating"),
                'rating_comment' => $request->input("${principleId}_rating_comment"),
                'is_na' => $request->input("${principleId}_is_na") ?? 0,
            ]);


            $principleAssessment = PrincipleAssessment::where('assessment_id', $latestAssessment->id)->where('principle_id', $principleId)->first();

            $sync = json_decode($request->input("scoreTags" . $principle->id));
            $syncPivot = [];

            if ($sync) {

                for ($i = 0, $iMax = count($sync); $i < $iMax; $i++) {
                    $syncPivot[] = ['assessment_id' => $latestAssessment->id];
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
                        $custom_score_tags[$i]['assessment_id'] = $latestAssessment->id;
                    }

                }

                $principleAssessment->customScoreTags()->createMany($custom_score_tags);
            }

        }

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
