<?php

namespace App\Mail;

use App\Models\Booking;
use App\Services\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
        // ✅ Resolve service here (SAFE)
        $pdf = app(TicketPdfService::class)->generate($this->booking);

        return $this->subject('Jetty Booking Confirmation')
            ->view('emails.booking_confirmation')
            ->attachData(
                $pdf->output(),
                'Jetty_Ticket_' . $this->booking->ticket_id . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
