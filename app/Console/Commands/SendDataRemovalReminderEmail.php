<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\RemovalRequest;
use Illuminate\Console\Command;
use App\Mail\DataRemovalReminder;
use Illuminate\Support\Facades\Mail;

class SendDataRemovalReminderEmail extends Command
{

    protected $signature = 'senddataremovalreminderemail';

    protected $description = 'Send data removal reminder email';

    public function handle(): int
    {
        // define number of days
        // cool down period: 30 days, send reminder 7 days before
        // 30 - 7 days = 23 days
        $numberOfDays = 23;

        // find data removal requests with status REQUESTED after requested for N days
        $removalRequests = RemovalRequest::where('status', 'REQUESTED')
                    ->where('requested_at', '<', Carbon::now()->subDays($numberOfDays))
                    ->get();

        $this->info($removalRequests->count() . ' data removal requests not yet cancelled or final confirmed after requested for ' . $numberOfDays . ' day(s)');

        $this->info($removalRequests->count() . ' reminder email sent to requester and site admin');

        foreach ($removalRequests as $removalRequest) {
            $toRecipients = [$removalRequest->requester_email, config('mail.data_removal_alert_recipients')];

            Mail::to($toRecipients)->queue(new DataRemovalReminder($removalRequest));

            $removalRequest->status = 'REMINDER_SENT';
            $removalRequest->reminded_at = Carbon::now();
            $removalRequest->save();    
        }

        return self::SUCCESS;
    }
}
