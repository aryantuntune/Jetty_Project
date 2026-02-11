<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;


class TicketPdfService
{
    // public function generate($booking)
    // {
    //     // QR content
    //     $qrData = url('/scan-ticket/' . $booking->ticket_id);

    //     // Generate QR PNG via trusted API (PNG output)
    //     $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='
    //         . urlencode($qrData);

    //     // Save locally
    //     $qrPath = storage_path('app/public/qrcodes/ticket_' . $booking->ticket_id . '.png');

    //     if (!file_exists(dirname($qrPath))) {
    //         mkdir(dirname($qrPath), 0755, true);
    //     }

    //     file_put_contents($qrPath, file_get_contents($qrImageUrl));

    //     // Generate PDF
    //     return Pdf::loadView('pdf.booking_ticket', [
    //         'booking' => $booking,
    //         'qrPath'  => $qrPath,
    //     ]);
    // }

    public function generate($booking)
    {
        // 1. QR image path - use qr_hash if available for security
        $qrIdentifier = $booking->qr_hash ?? $booking->ticket_id;
        $qrRelativePath = 'qrcodes/ticket_' . $booking->ticket_id . '.png';
        $qrFullPath = storage_path('app/public/' . $qrRelativePath);

        // 2. Generate QR Code content - use qr_hash for security (no ticket ID exposure)
        $qrContent = $booking->qr_hash
            ? $booking->qr_hash  // New secure: just the hash
            : url('/scan-ticket/' . $booking->ticket_id);  // Legacy: URL with ID

        // 3. Generate QR Code PNG (GD based, NO Imagick)
        Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->size(200)
            ->margin(5)
            ->build()
            ->saveToFile($qrFullPath);

        // 4. Generate PDF
        return Pdf::loadView('pdf.booking_ticket', [
            'booking' => $booking,
            'qrImage' => $qrRelativePath,
        ]);
    }
}
