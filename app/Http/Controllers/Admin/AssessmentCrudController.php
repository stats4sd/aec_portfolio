<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentStatus;
use App\Http\Controllers\Admin\Operations\AssessCustomOperation;
use App\Http\Controllers\Admin\Operations\RedlineOperation;
use App\Http\Controllers\Admin\Traits\UsesSaveAndNextAction;
use App\Http\Requests\AssessmentRequest;
use App\Models\Assessment;
use App\Models\AdditionalCriteriaScoreTag;
use App\Models\ScoreTag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Backpack\ReviseOperation\ReviseOperation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class AssessmentCrudController extends CrudController
{
    use CreateOperation;
    use UpdateOperation;

    use AuthorizesRequests;

    use RedlineOperation;
    use UsesSaveAndNextAction;

    use ReviseOperation;

    public function setup()
    {
        CRUD::setModel(Assessment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/assessment');
        CRUD::setEntityNameStrings('assessment', 'assessments');
    }

    public function setupAssessCustomOperation()
    {
        $this->authorize('assessProject', CRUD::getCurrentEntry()->project);

        Widget::add()->type('script')->content('assets/js/admin/forms/project_assess.js');

        CRUD::field('section-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title(function ($entry) {
                return "Assess Project: " . $entry->project->name;
            })
            ->content('
                    This additional section of the review asks about any additional criteria that your institution has added to this tool. While all projects across the system must be assessed against the 13 Principles of Agroecology, your institution may also have extra criteria that are important.
                    <br/><br/>
                    The form below asks about each of these custom criteria in turn. For each point, please enter:     <ul>
                            <li><b>A rating:</b> This is a number between 0 and 2, based on your appreciation of the value of the specific criteria in the project design / activities, and following the Spectrum defined by your institution. Decimal digits are allowed.</li>
                            <li><b>A comment:</b> Please add any comments to help explain the rating, and about how the criterion is seen within the project.</li>
                        </ul>
                        To help track progress, once a rating is given for a principle, that principle name will turn green. Once you have completed and reviewed every principle, please proceed to the final "Confirmation" tab, where you can mark the assessment as complete. Once done, you will return to the main projects list to view the final result.
          ');

        CRUD::enableVerticalTabs();
        // cannot use relationship with repeatable because we need to filter the scoretags...
        $entry = CRUD::getCurrentEntry();

        foreach ($entry->additionalCriteria as $assessmentCriterion) {

            $additionalCriteriaAssessment = $assessmentCriterion->additionalCriteriaAssessments()->where('assessment_id', $entry->id)->first();

            $ratingZeroDefintionRow = '<span class="text-secondary">This principle cannot be marked as not applicable</span>';
            if ($assessmentCriterion->can_be_na) {
                $ratingZeroDefintionRow = "
                                            <tr>
                                                <td>na</td>
                                                <td>$assessmentCriterion->rating_na</td>
                                            </tr>";
            }

            CRUD::field($assessmentCriterion->id . '_title')
                ->tab($assessmentCriterion->name)
                ->type('section-title')
                ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
                ->title($assessmentCriterion->name)
                ->content("<table class='table table - striped'>
                                <tr>
                                    <th>Score</th>
                                    <th>Definition</th>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>$assessmentCriterion->rating_two</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>$assessmentCriterion->rating_one</td>
                                </tr>
                                <tr>
                                    <td>0</td>
                                    <td>$assessmentCriterion->rating_zero</td>
                                </tr>
                                $ratingZeroDefintionRow
                            </table>");

            if ($assessmentCriterion->can_be_na) {
                CRUD::field($assessmentCriterion->id . "_is_na")
                    ->tab($assessmentCriterion->name)
                    ->attributes([
                        'data-to-disable' => $assessmentCriterion->id,
                    ])
                    ->type('boolean')
                    ->label('If this principle is not applicable for this project, tick this box.')
                    ->default($assessmentCriterion->pivot->is_na);

            }

            CRUD::field($assessmentCriterion->id . '_rating')
                ->tab($assessmentCriterion->name)
                ->label('Rating for ' . $assessmentCriterion->name)
                ->attributes([
                    'data-tab' => Str::slug($assessmentCriterion->name),
                    'data-update-tab' => '1',
                ])
                ->min(0)
                ->max(2)
                ->default($assessmentCriterion->pivot->rating);


            CRUD::field($assessmentCriterion->id . '_rating_comment')
                ->tab($assessmentCriterion->name)
                ->label('Comment for ' . $assessmentCriterion->name)
                ->hint('Please add a comment, even if the principle is not applicable to this project.')
                ->type('textarea')
                ->default($assessmentCriterion->pivot->rating_comment);

            // get existing score tags
            $tags = $assessmentCriterion->additionalCriteriaScoreTags()->whereHas('additionalCriteriaAssessment', function ($query) use ($entry) {
                $query->where('additional_criteria_assessment.assessment_id', $entry->id);
            })->pluck('id')->toArray();

            CRUD::field('additionalCriteriaScoreTags' . $assessmentCriterion->id)
                ->tab($assessmentCriterion->name)
                ->label('Presence of Examples/Indicators for ' . $assessmentCriterion->name)
                ->type('checklist_filtered')
                ->number_of_columns(1)
                ->model(AdditionalCriteriaScoreTag::class)
                ->options(function ($query) use ($assessmentCriterion) {
                    return $query->where('additional_criteria_id', $assessmentCriterion->id)->get()->pluck('name', 'id')->toArray();
                })
                ->default($tags);

            $customTags = $additionalCriteriaAssessment->additionalCriteriaCustomScoreTags
                ->map(fn($item) => ['name' => $item->name, 'id' => $item->id, 'description' => $item->description])
                ->toArray();

            CRUD::field('customScoreTags' . $assessmentCriterion->id)
                ->tab($assessmentCriterion->name)
                ->label('New Example/Indicator for ' . $assessmentCriterion->name)
                ->type('custom_table')
                ->columns([
                    'name' => 'Name',
                    'description' => 'Description (optional)'],
                )
                ->default($customTags);
        }

        CRUD::field('complete_title')
            ->type('section-title')
            ->tab('Confirm Assessment')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->content('
                Once you have completed the review of each assessment criteria, and are satisfied that the above entries are correct, please tick this box to confirm the review.<br/>
                <i>(Note: You may still edit this review after marking it as complete)</i>
            ');

        CRUD::field('assessment_incomplete_note')
            ->type('section-title')
            ->tab('Confirm Assessment')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->content('You have not given a rating for all assessment criteria. You must complete each entry above before marking the assessment as complete.')
            ->variant('warning');

        CRUD::field('assessment_complete')
            ->type('boolean')
            ->tab('Confirm Assessment')
            ->label('I confirm the assessment is complete')
            ->attributes([
                'data-check-complete' => '1',
            ])
            ->default($entry->assessment_status === AssessmentStatus::Complete);


        CRUD::field('assessment_incomplete')
            ->type('boolean')
            ->tab('Confirm Assessment')
            ->label('I confirm the assessment is complete')
            ->attributes([
                'disabled' => 'disabled',
                'readonly' => 'readonly',
            ]);

        $this->removeAllSaveActions();
        $this->addSaveAndReturnToProjectListAction();
        $this->addSaveAndNextAction('assess-custom', backpack_url('project'));

    }


    public function setupRedlineOperation()
    {
        $this->authorize('reviewRedlines', CRUD::getCurrentEntry()->project);

        Widget::add()->type('script')->content('assets/js/admin/forms/project_redlines.js');


        CRUD::setHeading('');
        $entry = CRUD::getCurrentEntry();

        CRUD::field('section-title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->title(function ($entry) {
                return "Assess Red Flags for " . $entry->project->name;
            })
            ->content('
                    Listed below is the set of red flags to check for each project.<br/><br/>
                    These are the Red Flag elements, which are counter-productive or harmful to the values and principles of agroecology. If any one of these is present in the project being rated, then the Agroecology Overall Score is 0.
                          ');

        // We cannot use the relationship with subfields field here, because we do not want the user to be able to unassign any redlines from the project.
        foreach ($entry->redLines as $redline) {
            CRUD::field('redline_title_' . $redline->id)
                ->wrapper([
                    'class' => 'col-md-6'
                ])
                ->type('custom_html')
                ->value("<h4>$redline->name</h4><p>$redline->description</p>");

            CRUD::field('redline_value_' . $redline->id)
                ->label('')
                ->default($redline->pivot->value ?? null)
                ->type('redline_radio')
                ->attributes([
                    'data-required' => '1',
                ])
                ->wrapper([
                    'class' => 'col-md-6',
                    'data-required-wrapper' => '1',
                ])
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ]);

            CRUD::field('redline_divider_' . $redline->id)
                ->type('custom_html')
                ->value('<hr/>');

        }

        CRUD::field('complete_title')
            ->type('section-title')
            ->view_namespace('stats4sd.laravel-backpack-section-title::fields')
            ->content('
                Once you have completed the review of each red flag, and are satisfied that the above entries are correct, please tick this box to confirm the review.<br/>
                <i>(Note: You may still edit this review after marking it as complete)</i>
                ');


        CRUD::field('redlines_complete')
            ->type('boolean')
            ->label('I confirm the Red Flags assessment is complete.')
            ->default(
                in_array($entry->redline_status,
                    [AssessmentStatus::Complete->value, AssessmentStatus::Failed->value], false));

        CRUD::field('redlines_incomplete')
            ->type('boolean')
            ->label('I confirm the Red Flags assessment is complete')
            ->hint('You must assign a value (or mark as NA) for every red flag above before marking the red flag assessment as complete.')
            ->attributes([
                'disabled' => 'disabled',
            ]);

        // save actions now hardcoded for redline operation
//        $this->removeAllSaveActions();
//        $this->addSaveAndReturnToProjectListAction();
    }
}
