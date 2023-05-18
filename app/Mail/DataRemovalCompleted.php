<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DataRemovalCompleted extends Mailable
{
    public $removalRequest;
    public $tentativeDataRemovalDate;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($removalRequest)
    {
        $this->removalRequest = $removalRequest;
        $this->tentativeDataRemovalDate = Carbon::parse($removalRequest->requested_at)->addDays(23)->toDateString();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject(config('app.name'). ': Data Removal Completed')
            ->markdown('emails.data_removal_completed');
    }
}
