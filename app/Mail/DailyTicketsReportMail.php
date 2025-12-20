<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyTicketsReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $date,
        public string $filePath,
        public string $fileName,
        public float $totalAmount
    ) {}

    public function build()
    {
        return $this->subject("Daily Ticket Report – {$this->date}")
            ->markdown('emails.reports.daily_tickets', [
                'date' => $this->date,
                'totalAmount' => $this->totalAmount,
            ])
            ->attach($this->filePath, [
                'as' => $this->fileName,
                'mime' => 'text/csv',
            ]);
    }
}

// --------------------------------------------------------------------2️⃣ In Hostinger hPanel → Cron Jobs

// Login to your Hostinger account.

// Go to Advanced → Cron Jobs (you’ll find it in the sidebar).

// Click Add Cron Job.

// Now you’ll see fields like Command, Schedule, etc.

// 3️⃣ Command to run

// In the Command box, enter (adjust path):

// /usr/bin/php /home/username/domains/yourdomain.com/public_html/artisan schedule:run >> /home/username/laravel-cron.log 2>&1


// ✅ Replace:

// username with your Hostinger account’s username.

// yourdomain.com with your domain name (or subdomain path if Laravel is in a subfolder).

// You can change the log file path/name if you want.

// for local run to send the mail immediately without waiting for schedule run :::
    // php artisan reports:send-daily-tickets