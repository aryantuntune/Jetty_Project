<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $pdf = Pdf::loadView('pdf.booking_ticket', [
            'booking' => $this->booking
        ]);

        return $this->subject('Jetty Booking Confirmation')
            ->view('emails.booking_confirmation')
            ->attachData(
                $pdf->output(),
                'Jetty_Ticket_' . $this->booking->ticket_id . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}