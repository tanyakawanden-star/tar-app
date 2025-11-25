<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TravelRequest;

class ExpensesSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $tr;

    public function __construct(TravelRequest $tr)
    {
        $this->tr = $tr;
    }

    public function build()
    {
        return $this->subject('Expenses Submitted: ' . $this->tr->tar_id)
                    ->view('emails.expenses_submitted');
    }
}
