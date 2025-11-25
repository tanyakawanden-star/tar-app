<?php

namespace App\Mail;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TravelRequestApproved extends Mailable
{
    use Queueable, SerializesModels;

    public TravelRequest $travelRequest;
    public User $approver;

    public function __construct(TravelRequest $travelRequest, User $approver)
    {
        $this->travelRequest = $travelRequest;
        $this->approver = $approver;
    }

    public function build()
    {
        return $this->subject('Travel Authorization Updated: ' . $this->travelRequest->tar_number)
            ->view('emails.travel_approved');
    }
}
