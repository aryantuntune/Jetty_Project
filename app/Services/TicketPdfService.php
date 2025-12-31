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
        // 1. QR image path
        $qrRelativePath = 'qrcodes/ticket_' . $booking->ticket_id . '.png';
        $qrFullPath = storage_path('app/public/' . $qrRelativePath);

        // 2. Generate QR Code (GD based, NO Imagick)
        Builder::create()
            ->writer(new PngWriter())
            ->data(url('/scan-ticket/' . $booking->ticket_id))
            ->size(200)
            ->margin(5)
            ->build()
            ->saveToFile($qrFullPath);

        // 3. Generate PDF
        return Pdf::loadView('pdf.booking_ticket', [
            'booking' => $booking,
            'qrImage' => $qrRelativePath, // pass relative path
        ]);
    }
}
