<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GrantSlipMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $scholar;
    public $scholarship;
    protected $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($scholar, $scholarship, $pdf)
    {
        $this->scholar = $scholar;
        $this->scholarship = $scholarship;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.grant-slip')
                    ->subject('Grant Release Notification - ' . $this->scholarship->scholarship_name)
                    ->attachData($this->pdf, 'Grant_Slip_' . $this->scholarship->scholarship_name . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
