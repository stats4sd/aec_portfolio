<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Principle;
use App\Models\PrincipleProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
            // $this->crud->addButton('top', 'assess', 'view', 'crud::buttons.assess');
            $this->crud->addButton('line', 'assess', 'view', 'crud::buttons.assess');
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
        $this->crud->hasAccessOrFail('assess');
        // $this->crud->setOperation('Assess');

        $id = $this->crud->getCurrentEntryId() ?? $id;


        $this->data['entry'] = $this->crud->getEntryWithLocale($id);
        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? 'Assess ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        // load the view
        return view("crud::operations.assess", $this->data);
    }

    public function postAssessForm(Request $request)
    {

        foreach (Principle::all() as $principle) {
            $principleId = $principle->id;


            $project = $this->crud->getEntry($request->input('id'));
            $project->principles()->updateExistingPivot($principleId, [
                    'rating' => $request->input("${principleId}_rating"),
                    'rating_comment' => $request->input("${principleId}_rating_comment"),
                ]);

            $principleProject = PrincipleProject::where('project_id', $project->id)->where('principle_id', $principleId)->first();
            $principleProject->scoreTags()->sync(json_decode($request->input($principle->id . "_scoreTags")));

        }

        return redirect($this->crud->route);
    }
}
