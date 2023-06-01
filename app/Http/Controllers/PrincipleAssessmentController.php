<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\PrincipleAssessment;
use Illuminate\Http\Request;

class PrincipleAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Assessment $assessment = null)
    {
        $query = PrincipleAssessment::with('assessment', 'principle.scoreTags', 'scoreTags', 'customScoreTags');

        if($assessment) {
            $query = $query->where('assessment_id', $assessment->id);
        }

        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
