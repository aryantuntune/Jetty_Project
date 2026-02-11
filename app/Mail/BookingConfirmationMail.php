<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Services\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        // Generate PDF for the ticket
        $pdf = app(TicketPdfService::class)->generate($this->ticket);

        $ticketNo = $this->ticket->ticket_no ?? $this->ticket->id;

        return $this->subject('Jetty Ferry - Booking Confirmation #' . $ticketNo)
            ->view('emails.booking_confirmation')
            ->with([
                'ticket' => $this->ticket,
                'ticketNo' => $ticketNo,
            ])
            ->attachData(
                $pdf->output(),
                'Jetty_Ticket_' . $ticketNo . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
