<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Branch;
use App\Models\FerryBoat;
use App\Models\User;
use App\Models\Booking;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StressTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stress-system {--count=3000 : Number of records to generate} {--type=mixed : mixed, ticket, or booking}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stress test the system by generating and verifying thousands of tickets and bookings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $type = $this->option('type');

        $this->info("Starting Stress Test: Generating {$count} records (Type: {$type})...");

        // 1. Setup Dependencies
        $branches = Branch::all();
        if ($branches->isEmpty()) {
            $this->error("No branches found. Please seed the database first.");
            return 1;
        }

        $ferryBoats = FerryBoat::all();
        if ($ferryBoats->isEmpty()) {
            $this->error("No ferry boats found. Please seed the database first.");
            return 1;
        }

        // Get a user to act as creator/checker
        $user = User::first();
        if (!$user) {
            $this->error("No users found. Please seed the database first.");
            return 1;
        }

        // Get a customer for bookings
        $customer = \App\Models\Customer::first();
        if (!$customer) {
            $customer = \App\Models\Customer::create([
                'first_name' => 'Test',
                'last_name' => 'Customer',
                'email' => 'test@example.com',
                'mobile' => '1234567890',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
            $this->info("Created test customer: {$customer->email}");
        }

        // 2. Generation Loop
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $startTime = microtime(true);
        $ticketsCreated = [];
        $bookingsCreated = [];

        DB::beginTransaction();

        try {
            for ($i = 0; $i < $count; $i++) {
                $isBooking = ($type === 'mixed') ? ($i % 2 === 0) : ($type === 'booking');

                $branch = $branches->random();
                $toBranch = $branches->where('id', '!=', $branch->id)->random();
                $boat = $ferryBoats->random();

                if ($isBooking) {
                    // Create Online Booking
                    $booking = Booking::create([
                        'ticket_id' => $this->generateTicketNo(),
                        'customer_id' => $customer->id, // Assumptions: Customer ID 1 exists or is nullable. Adjust if strictly enforced.
                        'ferry_id' => $boat->id,
                        'from_branch' => $branch->id,
                        'to_branch' => $toBranch->id,
                        'booking_date' => now()->toDateString(),
                        'departure_time' => now()->addHours(1)->format('H:i:s'), // Ensure future time
                        'items' => json_encode([['item_name' => 'Adult', 'quantity' => 1, 'rate' => 100, 'amount' => 100]]),
                        'total_amount' => 100,
                        'payment_id' => 'STRESS_TEST_' . Str::random(10),
                        'booking_source' => 'web',
                        'status' => 'success',
                    ]);
                    $bookingsCreated[] = $booking->id;

                } else {
                    // Create POS Ticket
                    $ticket = Ticket::create([
                        'branch_id' => $branch->id,
                        'ferry_boat_id' => $boat->id,
                        'payment_mode' => 'CASH MEMO',
                        'ferry_time' => now()->addHours(1), // Ensure future time
                        'total_amount' => 50,
                        'user_id' => $user->id,
                        'timestamp' => now(), // dummy
                        'ticket_date' => now()->toDateString(), // Ensure legacy field if needed
                        'ticket_no' => mt_rand(1000000, 9999999), // Random verify unique enough for test
                        'ferry_type' => 'NORMAL',
                        'no_of_units' => 1,
                    ]);
                    // Create a dummy line item
                    $ticket->lines()->create([
                        'item_name' => 'Adult',
                        'qty' => 1,
                        'rate' => 50,
                        'amount' => 50,
                        'user_id' => $user->id,
                        'levy' => 0
                    ]);

                    $ticketsCreated[] = $ticket->id;
                }

                $bar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Stress Test Create Failed: " . $e->getMessage());
            $this->error("Error creating records: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $endTime = microtime(true);
        $creationTime = $endTime - $startTime;

        $this->newLine();
        $this->info("Creation Complete in " . round($creationTime, 2) . "s");

        // 3. Verification Loop
        $this->info("Verifying all generated records...");

        $verifyBar = $this->output->createProgressBar(count($ticketsCreated) + count($bookingsCreated));
        $verifyBar->start();

        $startTimeVerify = microtime(true);
        $now = Carbon::now();

        // Bulk update is faster, but let's loop to simulate individual API hits if we wanted true stress, 
        // but for DB stress test, bulk update is also fine? 
        // Let's do batch updates to simulate 'fast scanning'.

        // Verify Bookings
        if (!empty($bookingsCreated)) {
            Booking::whereIn('id', $bookingsCreated)->update([
                'verified_at' => $now,
                'verified_by' => $user->id
            ]);
            $verifyBar->advance(count($bookingsCreated));
        }

        // Verify Tickets (Check if checker_id column exists or use correct column from analysis)
        // From analysis: TicketVerifyController uses 'checker_id' if schema has it, else relies on verified_by?
        // Let's check schema/model in logic. The controller checks `\Schema::hasColumn('tickets', 'checker_id')`.
        // We will assume it might, but update 'verified_at' mainly.

        if (!empty($ticketsCreated)) {
            $updateData = ['verified_at' => $now];
            // We can try setting checker_id if we want, but safe to just set verified_at for stress test basics.
            Ticket::whereIn('id', $ticketsCreated)->update($updateData);
            $verifyBar->advance(count($ticketsCreated));
        }

        $verifyBar->finish();
        $endTimeVerify = microtime(true);
        $verifyTime = $endTimeVerify - $startTimeVerify;

        $this->newLine();
        $this->info("Verification Complete in " . round($verifyTime, 2) . "s");

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Records', $count],
                ['Creation Time', round($creationTime, 2) . 's'],
                ['Verification Time', round($verifyTime, 2) . 's'],
                ['Throughput (Create)', round($count / $creationTime, 2) . ' rec/s'],
                ['Throughput (Verify)', round($count / $verifyTime, 2) . ' rec/s'],
            ]
        );

        return 0;
    }

    private function generateTicketNo()
    {
        $year = Carbon::now()->format('Y');
        $date = Carbon::now()->format('md');
        $random = mt_rand(100000, 999999);
        return "{$year}{$date}{$random}";
    }
}
