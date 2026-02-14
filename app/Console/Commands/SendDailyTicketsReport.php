<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\DailyTicketsReportMail;

class SendDailyTicketsReport extends Command
{
    protected $signature = 'reports:send-daily-tickets 
                            {--date= : YYYY-MM-DD (defaults to today IST)} 
                            {--email= : Override recipient email (optional)}';

    protected $description = 'Generate daily Ticket Details CSV and email it';

    public function handle(): int
    {
        // Date window (IST)
        $todayIst = Carbon::now('Asia/Kolkata')->toDateString();
        $date = $this->option('date') ?: $todayIst;

        // File path
        $filename = "tickets_{$date}.csv";
        $dir = "reports/daily";
        $path = "{$dir}/{$filename}";

        // Ensure dir exists
        Storage::makeDirectory($dir);

        // Build CSV stream to storage
        $full = Storage::path($path);
        $fh = fopen($full, 'w');

        // Header
        fputcsv($fh, [
            'Ticket Date (IST)','Ticket ID','Branch','Payment Mode',
            'Boat Name','Ferry Time (IST)','Ferry Type','Customer Name',
            'Customer Mobile','Total Amount'
        ]);

        $total = 0;

        Ticket::with(['branch','ferryBoat'])
            ->whereDate('created_at', $date)
            ->orderBy('created_at','asc')
            ->chunkById(1000, function ($chunk) use (&$total, $fh) {
                foreach ($chunk as $t) {
                    $total += (float)$t->total_amount;
                    fputcsv($fh, [
                        optional($t->created_at)->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        $t->id,
                        $t->branch->branch_name ?? '',
                        $t->payment_mode,
                        $t->ferryBoat->name ?? '',
                        optional($t->ferry_time)->timezone('Asia/Kolkata')->format('Y-m-d H:i'),
                        $t->ferry_type,
                        $t->customer_name ?? '',
                        $t->customer_mobile ?? '',
                        number_format((float)$t->total_amount, 2, '.', ''),
                    ]);
                }
            }, 'id');

        fclose($fh);

        // Email
        $to = $this->option('email') ?: 'aryantuntune42@gmail.com';
        Mail::to($to)->send(new DailyTicketsReportMail(
            date: $date,
            filePath: $full,
            fileName: $filename,
            totalAmount: $total
        ));

        $this->info("Daily tickets CSV emailed to {$to} for {$date} ({$filename}). Total = {$total}");

        return self::SUCCESS;
    }
}