<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use Prologue\Alerts\Facades\Alert;

class AssessmentController extends Controller
{

    public function assess(Assessment $assessment)
    {
        return view("crud::operations.assess", ['assessment' => $assessment]);
    }

    public function assessCustom(Assessment $assessment)
    {
        return view("crud::operations.assess-custom", ['assessment' => $assessment]);
    }

    public function finaliseAssessment(Assessment $assessment)
    {
        if ($assessment->principleAssessments->every(fn($pa) => $pa->complete)) {
            $assessment->principle_status = AssessmentStatus::Complete;
            $assessment->save();

            Alert::success('The Principles Assessment for ' . $assessment->project->name . ' is now complete.')->flash();
            return redirect('/admin/project');
        }

        Alert::warning('It looks like the assessment is not yet complete. Please check you have given a rating to all applicable principles.')->flash();
        return back();

    }

    public function finaliseAssessmentCustom(Assessment $assessment)
    {
        if ($assessment->additionalCriteriaAssessment->every(fn($ca) => $ca->complete)) {
            $assessment->additional_status = AssessmentStatus::Complete;
            $assessment->save();

            Alert::success('The Additional Criteria Assessment for ' . $assessment->project->name . ' is now complete.')->flash();
            return redirect('/admin/project');
        }

        Alert::warning('It looks like the assessment for additional criteria is not yet complete. Please check you have given a rating to all applicable criteria.')->flash();
        return back();


    }

}
