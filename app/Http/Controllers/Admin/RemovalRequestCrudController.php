<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Assessment;
use App\Models\RemovalRequest;
use App\Mail\DataRemovalReminder;
use App\Mail\DataRemovalCancelled;
use App\Mail\DataRemovalFinalConfirmed;
use App\Mail\DataRemovalCompleted;
use App\Models\OrganisationMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RemovalRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RemovalRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RemovalRequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\RemovalRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/removal-request');
        CRUD::setEntityNameStrings('removal request', 'removal requests');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // add custom buttons

        // Although we have a daily schedule job to send data removal reminder email, 
        // we keep the Remind button here in case we need to resend reminder email to requester.
        // This is common that people claim they have not received email and ask the sender to re-send...
        $this->crud->addButtonFromView('line', 'remind-data-removal', 'remind-data-removal', 'start');
        $this->crud->addButtonFromView('line', 'cancel-data-removal', 'cancel-data-removal', 'start');
        $this->crud->addButtonFromView('line', 'confirm-data-removal', 'confirm-data-removal', 'start');
        $this->crud->addButtonFromView('line', 'perform-data-removal', 'perform-data-removal', 'start');

        CRUD::column('organisation_name')->label('Organisation');
        CRUD::column('requester_name')->label('Requester');;
        CRUD::column('status');
        CRUD::column('requested_at');
    }

    public function cancel($id) {
        $removalRequest = RemovalRequest::find($id);

        // update status
        $removalRequest->status = 'CANCELLED';
        $removalRequest->cancelled_at = Carbon::now();
        $removalRequest->save();

        // send reminder email to requester
        $toRecipients = [$removalRequest->requester_email, config('mail.data_removal_alert_recipients')];

        Mail::to($toRecipients)->queue(new DataRemovalCancelled($removalRequest));

        // refresh CRUD panel
        return back();
    }

    public function remind($id) {
        $removalRequest = RemovalRequest::find($id);

        // update status
        $removalRequest->status = 'REMINDER_SENT';
        $removalRequest->reminded_at = Carbon::now();
        $removalRequest->save();

        // send reminder email to requester
        $toRecipients = [$removalRequest->requester_email, config('mail.data_removal_alert_recipients')];

        Mail::to($toRecipients)->queue(new DataRemovalReminder($removalRequest));

        // refresh CRUD panel
        return back();
    }

    public function confirm($id) {
        $removalRequest = RemovalRequest::find($id);

        // update status
        $removalRequest->status = 'FINAL_CONFIRMED';
        $removalRequest->final_confirmed_at = Carbon::now();
        $removalRequest->save();

        // send reminder email to requester
        $toRecipients = [$removalRequest->requester_email, config('mail.data_removal_alert_recipients')];

        Mail::to($toRecipients)->queue(new DataRemovalFinalConfirmed($removalRequest));

        // refresh CRUD panel
        return back();
    }

    public function perform($id) {

        // TODO: some table and column names need to be revised after DB schema change

        // To avoid spending too much time on this feature, below functionalities have not been developed yet.
        // As they are involved in the procedure that is likely lead to a long discussion...
        // Do we need to:
        // 1. Remind requester for data removal?
        // 2. Ask requester to final confirm data removal? (And how? By replying system generated email?)
        // 3. Need someone (site admin / site manager) to manually trigger to remove everything for an institution?

        $removalRequest = RemovalRequest::find($id);

        // update status
        $removalRequest->status = 'EVERYTHING_REMOVED';
        $removalRequest->everything_removed_at = Carbon::now();
        $removalRequest->save();

        $organisationId = $removalRequest->organisation_id;
        // logger($organisationId);

        $projectIds = Project::select('id')->where('organisation_id', $organisationId)->get()->pluck('id');
        // logger($projectIds);

        $assessmentIds = Assessment::select('id')->whereIn('project_id', $projectIds)->get()->pluck('id');
        // logger($assessmentIds);

        $userIds = OrganisationMember::select('user_id')->where('organisation_id', $organisationId)->get()->pluck('user_id');
        // logger($userIds);


        // run custom SQL directly as we do not have model class for each table

        // TODO: remove assessment related records
        // related tables: assessment_red_line, principle_assessment, custom_score_tags, principle_project_score_tag, assessments
        // TBC: table names and column names to be confirmed

        if (count($assessmentIds) != 0) {
            DB::statement('DELETE FROM assessment_red_line WHERE assessment_id IN ' . $this->getSQLArray($assessmentIds));
            DB::statement('DELETE FROM principle_assessment WHERE assessment_id IN ' . $this->getSQLArray($assessmentIds));
            // DB::statement('DELETE FROM custom_score_tags WHERE assessment_id IN ' . $this->getSQLArray($assessmentIds));
            // DB::statement('DELETE FROM principle_project_score_tag WHERE assessment_id IN ' . $this->getSQLArray($assessmentIds));
            DB::statement('DELETE FROM assessments WHERE id IN ' . $this->getSQLArray($assessmentIds));
        }


        // TODO: remove custom principles related records (table to be added)

        
        // TODO: remove project related records
        // related tables: continent_project, country_project, project_region, portfolios, projects
        if (count($projectIds) != 0) {
            DB::statement('DELETE FROM continent_project WHERE project_id IN ' . $this->getSQLArray($projectIds));
            DB::statement('DELETE FROM country_project WHERE project_id IN ' . $this->getSQLArray($projectIds));
            DB::statement('DELETE FROM project_region WHERE project_id IN ' . $this->getSQLArray($projectIds));
        }

        DB::statement('DELETE FROM portfolios WHERE organisation_id = ' . $organisationId);
        DB::statement('DELETE FROM projects WHERE organisation_id = ' . $organisationId);


        // TODO: remove organisation related records
        // related tables: organisation_members, users, organisations
        DB::statement('DELETE FROM organisation_members WHERE organisation_id = ' . $organisationId);

        if (count($userIds) != 0) {
            DB::statement('DELETE FROM users WHERE id IN ' . $this->getSQLArray($userIds));
        }

        DB::statement('DELETE FROM organisations WHERE id = ' . $organisationId);

        // send reminder email to requester
        $toRecipients = [$removalRequest->requester_email, config('mail.data_removal_alert_recipients')];

        Mail::to($toRecipients)->queue(new DataRemovalCompleted($removalRequest));

        // refresh CRUD panel
        return back();
    }


    protected function getSQLArray($array) {
        $value = $array . '';

        // replace [] to ()
        $result = str_replace('[', '(', str_replace(']', ')', $value));

        return $result;
    }

}
