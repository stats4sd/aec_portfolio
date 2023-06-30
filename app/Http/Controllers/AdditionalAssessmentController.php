<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdditionalCriteriaAssessmentRequest;
use App\Models\Assessment;
use App\Models\CustomScoreTag;
use App\Models\AdditionalCriteriaAssessment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdditionalAssessmentController extends Controller
{

    public function index(Assessment $assessment = null): Collection
    {
        $query = AdditionalCriteriaAssessment::with(
            'assessment',
            'additionalCriteria.additionalCriteriaScoreTags',
            'additionalCriteriaScoreTags',
            'additionalCriteriaCustomScoreTags'
        );

        if ($assessment) {
            $query = $query->where('assessment_id', $assessment->id);
        }

        return $query->get()
            ->map(function (AdditionalCriteriaAssessment $additionalCriteriaAssessment) {
                $additionalCriteriaAssessment->score_tag_ids = $additionalCriteriaAssessment->additionalCriteriaScoreTags->pluck('id');
                return $additionalCriteriaAssessment;
            });
    }

    public function update(AdditionalCriteriaAssessmentRequest $request, AdditionalCriteriaAssessment $additionalCriteriaAssessment)
    {
        // handle main info
        $additionalCriteriaAssessment->update($request->validated());

        // handle score tags + custom score tags relationships
        $syncTags = collect($request->input('score_tag_ids'))->mapWithKeys(fn($tag) => [$tag => ['assessment_id' => $additionalCriteriaAssessment->assessment_id]]);

        $additionalCriteriaAssessment->additionalCriteriaScoreTags()->sync($syncTags);

        $requestCustomTags = collect($request->input('custom_score_tags'))->filter(fn(array $tag): bool => $tag['name'] !== null && $tag['name'] !== '');

        // delete custom score tags that are not in the request
        foreach ($additionalCriteriaAssessment->additionalCriteriaCustomScoreTags as $customScoreTag) {
            if ($requestCustomTags->pluck('id')->doesntContain($customScoreTag->id)) {
                $customScoreTag->delete();
            } else {
                $updated = $requestCustomTags->firstWhere('id', $customScoreTag->id);

                $customScoreTag->update([
                    'name' => $updated['name']
                ]);
            }
        }

        // create new tags
        foreach ($requestCustomTags as $custom_score_tag) {
            if (!isset($custom_score_tag['id'])) {
                $additionalCriteriaAssessment->additionalCriteriaCustomScoreTags()->create([
                    'name' => $custom_score_tag['name'],
                    'assessment_id' => $additionalCriteriaAssessment->assessment_id,
                ]);
            }
        }


        return $additionalCriteriaAssessment;
    }


    public function destroy(string $id)
    {
        //
    }
}
