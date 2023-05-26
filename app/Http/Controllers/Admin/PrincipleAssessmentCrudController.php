<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PrincipleAssessmentRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\ReviseOperation\ReviseOperation;

/**
 * Class PrincipleAssessmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PrincipleAssessmentCrudController extends CrudController
{
    use ListOperation;
    use ReviseOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\PrincipleAssessment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/principle-assessment');
        CRUD::setEntityNameStrings('principle assessment', 'principle assessments');
    }
    protected function setupListOperation()
    {
        CRUD::column('assessment.project.name');
        CRUD::column('principle.name');
        CRUD::column('is_na');
        CRUD::column('rating');
        CRUD::column('rating_comment');
        CRUD::column('updated_at');

    }
}
