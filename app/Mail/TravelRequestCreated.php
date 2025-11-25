<?php

namespace App\Mail;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TravelRequestCreated extends Mailable
{
    use Queueable, SerializesModels;

    public TravelRequest $travelRequest;
    public ?User $recipient;

    public function __construct(TravelRequest $travelRequest, ?User $recipient = null)
    {
        $this->travelRequest = $travelRequest;
        $this->recipient = $recipient;
    }

    public function build()
    {
        return $this->subject('New Travel Authorization Request: ' . $this->travelRequest->tar_number)
            ->view('emails.travel_created');
    }
}
