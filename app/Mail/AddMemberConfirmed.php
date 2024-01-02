<?php

namespace App\Mail;

use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddMemberConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $inviterName;
    public $inviterEmail;
    public $organisationName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($inviterName, $inviterEmail, $organisationName)
    {
        $this->inviterName = $inviterName;
        $this->inviterEmail = $inviterEmail;
        $this->organisationName = $organisationName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
        ->subject(config('app.name'). ': Invitation To Join Institution ' . $this->organisationName)
        ->markdown('emails.invite_confirmed');
    }
}
