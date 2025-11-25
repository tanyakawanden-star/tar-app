<?php

namespace App\Mail;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TravelRequestRejected extends Mailable
{
    use Queueable, SerializesModels;

    public TravelRequest $travelRequest;
    public User $approver;
    public string $reason;

    public function __construct(TravelRequest $travelRequest, User $approver, string $reason)
    {
        $this->travelRequest = $travelRequest;
        $this->approver = $approver;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Travel Authorization Rejected: ' . $this->travelRequest->tar_number)
            ->view('emails.travel_rejected');
    }
}
