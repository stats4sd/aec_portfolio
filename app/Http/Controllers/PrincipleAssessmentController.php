<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrincipleAssessmentRequest;
use App\Models\Assessment;
use App\Models\CustomScoreTag;
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
            ->map(function (PrincipleAssessment $principleAssessment) {
                $principleAssessment->score_tag_ids = $principleAssessment->scoreTags->pluck('id');
                $principleAssessment->complete = $principleAssessment->rating !== null || $principleAssessment->is_na;
                return $principleAssessment;
            });
    }

    public function update(PrincipleAssessmentRequest $request, PrincipleAssessment $principle_assessment)
    {
        // handle main info
        $principle_assessment->update($request->validated());

        // handle score tags + custom score tags relationships

        $request->validate(
            ['score_tag_ids.*' => ['integer', 'exists:score_tags,id']],
            ['custom_score_tags.*.name' => ['required']],
        );

        $syncTags = collect($request->input('score_tag_ids'))->mapWithKeys(fn($tag) => [$tag => ['assessment_id' => $principle_assessment->assessment_id]]);

        $principle_assessment->scoreTags()->sync($syncTags);

        $requestCustomTags = collect($request->input('custom_score_tags'));

        // delete custom score tags that are not in the request
        foreach($principle_assessment->customScoreTags as $customScoreTag) {
            if($requestCustomTags->pluck('id')->doesntContain($customScoreTag->id)) {
                $customScoreTag->delete();
            } else {
                $updated = $requestCustomTags->firstWhere('id', $customScoreTag->id);

                $customScoreTag->update([
                    'name' => $updated['name']
                ]);
            }
        }

        // create new tags
        foreach($requestCustomTags as $custom_score_tag) {
            if(!isset($custom_score_tag['id'])) {
                $principle_assessment->customScoreTags()->create([
                    'name' => $custom_score_tag['name'],
                    'assessment_id' => $principle_assessment->assessment_id,
                ]);
            }
        }


        return $principle_assessment;
    }


    public function destroy(string $id)
    {
        //
    }
}
