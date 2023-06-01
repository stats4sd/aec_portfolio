<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrincipleAssessmentRequest;
use App\Models\Assessment;
use App\Models\PrincipleAssessment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PrincipleAssessmentController extends Controller
{

    public function index(Assessment $assessment = null): Collection
    {
        $query = PrincipleAssessment::with('assessment', 'principle.scoreTags', 'scoreTags', 'customScoreTags');

        if ($assessment) {
            $query = $query->where('assessment_id', $assessment->id);
        }

        return $query->get()
            ->map(function ($principleAssessment) {
                $principleAssessment->score_tag_ids = $principleAssessment->scoreTags->pluck('id');
                return $principleAssessment;
            });
    }

    public function update(PrincipleAssessmentRequest $request, PrincipleAssessment $principle_assessment)
    {
        // handle main info
        $principle_assessment->update($request->validated());

        // handle score tags + custom score tags relationships

        $request->validate(
            ['score_tag_ids.*' => ['integer', 'exists:score_tags,id']]
        );

        $syncTags = collect($request->input('score_tag_ids'))->mapWithKeys(fn($tag) => [$tag => ['assessment_id' => $principle_assessment->assessment_id]]);

        $principle_assessment->scoreTags()->sync($syncTags);


        return $principle_assessment;
    }


    public function destroy(string $id)
    {
        //
    }
}
