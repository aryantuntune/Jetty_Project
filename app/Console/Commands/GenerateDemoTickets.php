<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateDemoTickets extends Command
{
    protected $signature = 'demo:tickets {count=10000 : Number of tickets to generate}';
    protected $description = 'Generate demo tickets for testing';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Generating {$count} demo tickets...");

        // Get existing data
        $branches = DB::table('branches')->pluck('id')->toArray();
        $ferryboats = DB::table('ferryboats')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();
        $itemRates = DB::table('item_rates')->get();

        if (empty($branches) || empty($ferryboats) || empty($users) || $itemRates->isEmpty()) {
            $this->error('Missing required data. Make sure branches, ferryboats, users, and item_rates exist.');
            return 1;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        // Generate tickets in batches
        $batchSize = 500;
        $ticketNumber = DB::table('tickets')->max('id') ?? 0;

        for ($i = 0; $i < $count; $i += $batchSize) {
            $tickets = [];
            $currentBatch = min($batchSize, $count - $i);

            for ($j = 0; $j < $currentBatch; $j++) {
                $ticketNumber++;
                $branchId = $branches[array_rand($branches)];
                $ferryboatId = $ferryboats[array_rand($ferryboats)];
                $userId = $users[array_rand($users)];

                // Random date in last 6 months
                $createdAt = Carbon::now()->subDays(rand(1, 180))->setTime(rand(6, 18), rand(0, 59));

                // Random number of items (1-5)
                $numItems = rand(1, 5);
                $totalAmount = 0;
                $ticketLines = [];

                for ($k = 0; $k < $numItems; $k++) {
                    $item = $itemRates->random();
                    $qty = rand(1, 4);
                    $rate = $item->rate ?? rand(20, 500);
                    $amount = $qty * $rate;
                    $totalAmount += $amount;

                    $ticketLines[] = [
                        'item_rate_id' => $item->id,
                        'item_name' => $item->item_name ?? 'Item',
                        'qty' => $qty,
                        'rate' => $rate,
                        'amount' => $amount,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                $tickets[] = [
                    'ticket_number' => 'TKT-' . str_pad($ticketNumber, 8, '0', STR_PAD_LEFT),
                    'branch_id' => $branchId,
                    'ferryboat_id' => $ferryboatId,
                    'user_id' => $userId,
                    'total_amount' => $totalAmount,
                    'payment_mode' => ['cash', 'upi', 'card'][rand(0, 2)],
                    'status' => ['completed', 'completed', 'completed', 'pending'][rand(0, 3)],
                    'ticket_date' => $createdAt->toDateString(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            // Insert tickets
            DB::table('tickets')->insert($tickets);

            // Get inserted ticket IDs
            $insertedIds = DB::table('tickets')
                ->orderBy('id', 'desc')
                ->limit($currentBatch)
                ->pluck('id')
                ->reverse()
                ->values();

            // Insert ticket lines
            $allLines = [];
            foreach ($insertedIds as $index => $ticketId) {
                $numItems = rand(1, 5);
                for ($k = 0; $k < $numItems; $k++) {
                    $item = $itemRates->random();
                    $qty = rand(1, 4);
                    $rate = $item->rate ?? rand(20, 500);

                    $allLines[] = [
                        'ticket_id' => $ticketId,
                        'item_rate_id' => $item->id,
                        'item_name' => $item->item_name ?? 'Item',
                        'qty' => $qty,
                        'rate' => $rate,
                        'amount' => $qty * $rate,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($allLines)) {
                // Insert in chunks to avoid memory issues
                foreach (array_chunk($allLines, 1000) as $chunk) {
                    DB::table('ticket_lines')->insert($chunk);
                }
            }

            $bar->advance($currentBatch);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$count} demo tickets!");

        // Show summary
        $totalTickets = DB::table('tickets')->count();
        $totalLines = DB::table('ticket_lines')->count();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Tickets', number_format($totalTickets)],
                ['Total Ticket Lines', number_format($totalLines)],
            ]
        );

        return 0;
    }
}
