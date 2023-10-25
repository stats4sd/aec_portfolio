<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Carbon\Carbon;
use App\Models\Organisation;
use App\Models\RemovalRequest;
use App\Mail\DataRemovalReminder;
use App\Mail\DataRemovalCancelled;
use App\Mail\DataRemovalCompleted;
use App\Models\OrganisationMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\DataRemovalFinalConfirmed;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RemovalRequestCrudController extends AdminPanelCrudController
{
    use ListOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\RemovalRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/removal-request');
        CRUD::setEntityNameStrings('removal request', 'removal requests');

        parent::setup();
    }

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

        CRUD::column('organisation.name')->label('Organisation');
        CRUD::column('requester.name')->label('Requester');
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
        $toRecipients = [$removalRequest->requester->email, config('mail.data_removal_alert_recipients')];

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
        $toRecipients = [$removalRequest->requester->email, config('mail.data_removal_alert_recipients')];

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
        $toRecipients = [$removalRequest->requester->email, config('mail.data_removal_alert_recipients')];

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

        // send reminder email to requester
        $toRecipients = [$removalRequest->requester->email, config('mail.data_removal_alert_recipients')];

        Mail::to($toRecipients)->queue(new DataRemovalCompleted($removalRequest));


        $organisationId = $removalRequest->organisation_id;

        $userIds = OrganisationMember::select('user_id')->where('organisation_id', $organisationId)->get()->pluck('user_id');

        if (count($userIds) != 0) {
            DB::statement('DELETE FROM users WHERE id IN ' . $this->getSQLArray($userIds));
        }

        // delete organisation record, ON DELETE constraint will delete related records in other tables
        Organisation::destroy($removalRequest->organisation_id);

        // refresh CRUD panel
        return back();
    }


    protected function getSQLArray($array) {
        $value = $array . '';

        // replace [] to ()
        return str_replace('[', '(', str_replace(']', ')', $value));
    }

}
