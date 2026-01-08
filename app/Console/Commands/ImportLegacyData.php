<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class ImportLegacyData extends Command
{
    protected $signature = 'legacy:import {--table=all : Table to import (all, branches, ferryboats, schedules, categories, items, guests, vehicles, tickets)}';
    protected $description = 'Import data from legacy SQL Server database (MTMS_e)';

    private $sqlcmdPath = 'C:/Program Files (x86)/Microsoft SQL Server/100/Tools/Binn/sqlcmd.exe';
    private $tempDir;

    public function handle()
    {
        $table = $this->option('table');

        $this->tempDir = storage_path('app/legacy_import');
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $this->info("Starting legacy data import...");
        $this->info("Using sqlcmd at: {$this->sqlcmdPath}");

        $tables = $table === 'all'
            ? ['branches', 'ferryboats', 'schedules', 'categories', 'items', 'guests', 'vehicles', 'tickets']
            : [$table];

        foreach ($tables as $t) {
            $method = 'import' . ucfirst($t);
            if (method_exists($this, $method)) {
                $this->$method();
            } else {
                $this->warn("Unknown table: $t");
            }
        }

        $this->info("\nImport completed!");
        return 0;
    }

    private function querySqlServer($sql)
    {
        $tempFile = $this->tempDir . '/query_result.txt';

        // Escape the SQL for command line
        $sql = str_replace('"', '""', $sql);

        $cmd = "\"{$this->sqlcmdPath}\" -S \"(local)\\SQLEXPRESS\" -E -d MTMS_e -Q \"{$sql}\" -s \"|\" -W -h -1 -o \"{$tempFile}\"";

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error("SQL Server query failed with code: $returnCode");
            return [];
        }

        if (!file_exists($tempFile)) {
            return [];
        }

        $content = file_get_contents($tempFile);
        $lines = explode("\n", trim($content));

        $results = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, 'rows affected') !== false) {
                continue;
            }
            $results[] = explode('|', $line);
        }

        unlink($tempFile);
        return $results;
    }

    private function importBranches()
    {
        $this->info("\n--- Importing Branches ---");

        $results = $this->querySqlServer("
            SELECT BranchID, BranchName, BranchAddress, BranchPhone,
                   DestBranchID, DestBranchName, FerryBoatID
            FROM SCTK_MAST_Branches
            WHERE BranchID != 99
        ");

        $this->info("Found " . count($results) . " branches in legacy database.");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('branches')->truncate();
        DB::table('legacy_id_mappings')->where('table_name', 'branches')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $bar = $this->output->createProgressBar(count($results));

        foreach ($results as $row) {
            if (count($row) < 7) continue;

            $id = DB::table('branches')->insertGetId([
                'branch_id' => (int)trim($row[0]),
                'branch_name' => trim($row[1]),
                'branch_address' => trim($row[2] ?? ''),
                'branch_phone' => trim($row[3] ?? ''),
                'dest_branch_id' => (int)trim($row[4]) ?: null,
                'dest_branch_name' => trim($row[5] ?? ''),
                'ferry_boat_id' => (int)trim($row[6]) ?: null,
                'user_id' => 1,
                'is_active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('legacy_id_mappings')->insert([
                'table_name' => 'branches',
                'legacy_id' => trim($row[0]),
                'new_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported " . count($results) . " branches.");
    }

    private function importFerryboats()
    {
        $this->info("\n--- Importing Ferryboats ---");

        $results = $this->querySqlServer("
            SELECT FerryBoatID, FerryBoatNo, FerryBoatName, activeflag
            FROM SCTK_MAST_FerryBoats
        ");

        $this->info("Found " . count($results) . " ferryboats in legacy database.");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('ferryboats')->truncate();
        DB::table('legacy_id_mappings')->where('table_name', 'ferryboats')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $bar = $this->output->createProgressBar(count($results));

        $firstBranch = DB::table('branches')->first();

        foreach ($results as $row) {
            if (count($row) < 4) continue;

            $id = DB::table('ferryboats')->insertGetId([
                'number' => trim($row[1]),
                'name' => trim($row[2]),
                'user_id' => 1,
                'branch_id' => $firstBranch->id ?? 1,
                'is_active' => trim($row[3]) ?: 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('legacy_id_mappings')->insert([
                'table_name' => 'ferryboats',
                'legacy_id' => trim($row[0]),
                'new_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported " . count($results) . " ferryboats.");
    }

    private function importSchedules()
    {
        $this->info("\n--- Importing Ferry Schedules ---");

        $results = $this->querySqlServer("
            SELECT FerryScheduleID, FerryScheduleHour, FerryScheduleMinute,
                   CONVERT(VARCHAR(8), FerryScheduleTime, 108) as ScheduleTime, activeflag
            FROM SCTK_MAST_FerrySchedules
        ");

        $this->info("Found " . count($results) . " schedules in legacy database.");

        DB::table('ferry_schedules')->truncate();
        DB::table('legacy_id_mappings')->where('table_name', 'ferry_schedules')->delete();

        $bar = $this->output->createProgressBar(count($results));

        foreach ($results as $row) {
            if (count($row) < 5) continue;

            $id = DB::table('ferry_schedules')->insertGetId([
                'hour' => (int)trim($row[1]),
                'minute' => (int)trim($row[2]),
                'schedule_time' => trim($row[3]) ?: null,
                'is_active' => trim($row[4]) ?: 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('legacy_id_mappings')->insert([
                'table_name' => 'ferry_schedules',
                'legacy_id' => trim($row[0]),
                'new_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported " . count($results) . " ferry schedules.");
    }

    private function importCategories()
    {
        $this->info("\n--- Importing Item Categories ---");

        $results = $this->querySqlServer("
            SELECT ItemCategoryID, ItemCategoryName, ItemCategoryLevy, activeflag
            FROM SCTK_MAST_ItemCategories
        ");

        $this->info("Found " . count($results) . " item categories in legacy database.");

        DB::table('item_categories')->truncate();
        DB::table('legacy_id_mappings')->where('table_name', 'item_categories')->delete();

        foreach ($results as $row) {
            if (count($row) < 4) continue;

            $id = DB::table('item_categories')->insertGetId([
                'category_name' => trim($row[1]),
                'levy' => (float)trim($row[2]) ?: 0,
                'user_id' => 1,
                'is_active' => trim($row[3]) ?: 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('legacy_id_mappings')->insert([
                'table_name' => 'item_categories',
                'legacy_id' => trim($row[0]),
                'new_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info("Imported " . count($results) . " item categories.");

        // Guest Categories
        $this->info("\n--- Importing Guest Categories ---");

        $results = $this->querySqlServer("
            SELECT GuestCategoryID, GuestCategoryName, activeflag
            FROM SCTK_MAST_GuestCategories
        ");

        DB::table('guest_categories')->truncate();
        DB::table('legacy_id_mappings')->where('table_name', 'guest_categories')->delete();

        foreach ($results as $row) {
            if (count($row) < 3) continue;

            $id = DB::table('guest_categories')->insertGetId([
                'name' => trim($row[1]),
                'user_id' => 1,
                'is_active' => trim($row[2]) ?: 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('legacy_id_mappings')->insert([
                'table_name' => 'guest_categories',
                'legacy_id' => trim($row[0]),
                'new_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info("Imported " . count($results) . " guest categories.");
    }

    private function importItems()
    {
        $this->info("\n--- Importing Item Rates ---");

        $results = $this->querySqlServer("
            SELECT ItemID, ItemCategoryID, ItemName, ItemShortName,
                   ItemRate, ItemLevy, ItemSurchargeP, LevySurchargeP,
                   SpaceUnits, IsFixedRate, IsVehicle, activeflag
            FROM SCTK_MAST_Items
        ");

        $this->info("Found " . count($results) . " items in legacy database.");

        DB::table('item_rates')->truncate();
        DB::table('legacy_id_mappings')->where('table_name', 'item_rates')->delete();

        $categoryMappings = DB::table('legacy_id_mappings')
            ->where('table_name', 'item_categories')
            ->pluck('new_id', 'legacy_id')
            ->toArray();

        $bar = $this->output->createProgressBar(count($results));

        foreach ($results as $row) {
            if (count($row) < 12) continue;

            $categoryId = $categoryMappings[trim($row[1])] ?? null;

            $id = DB::table('item_rates')->insertGetId([
                'item_name' => trim($row[2]),
                'item_short_name' => trim($row[3] ?? ''),
                'item_category_id' => $categoryId,
                'item_rate' => (float)trim($row[4]) ?: 0,
                'item_lavy' => (float)trim($row[5]) ?: 0,
                'item_surcharge_pct' => (float)trim($row[6]) ?: 0,
                'levy_surcharge_pct' => (float)trim($row[7]) ?: 0,
                'space_units' => (float)trim($row[8]) ?: 1,
                'is_fixed_rate' => trim($row[9]) ?: 'N',
                'is_vehicle' => trim($row[10]) ?: 'N',
                'is_active' => trim($row[11]) ?: 'Y',
                'starting_date' => '2023-04-01',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('legacy_id_mappings')->insert([
                'table_name' => 'item_rates',
                'legacy_id' => trim($row[0]),
                'new_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported " . count($results) . " item rates.");
    }

    private function importGuests()
    {
        $this->info("\n--- Importing Guests ---");

        $results = $this->querySqlServer("
            SELECT GuestID, GuestCategoryID, GuestName, GuestAddress,
                   GuestPhone, GuestDesignation, GuestRemark, activeflag
            FROM SCTK_MAST_Guests
        ");

        $this->info("Found " . count($results) . " guests in legacy database.");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('guests')->truncate();
        DB::table('legacy_id_mappings')->where('table_name', 'guests')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categoryMappings = DB::table('legacy_id_mappings')
            ->where('table_name', 'guest_categories')
            ->pluck('new_id', 'legacy_id')
            ->toArray();

        $bar = $this->output->createProgressBar(count($results));

        foreach ($results as $row) {
            if (count($row) < 8) continue;

            $categoryId = $categoryMappings[trim($row[1])] ?? 1;

            $id = DB::table('guests')->insertGetId([
                'name' => trim($row[2]),
                'category_id' => $categoryId,
                'address' => trim($row[3] ?? ''),
                'phone' => trim($row[4] ?? ''),
                'designation' => trim($row[5] ?? ''),
                'remark' => trim($row[6] ?? ''),
                'user_id' => 1,
                'branch_id' => 1,
                'is_active' => trim($row[7]) ?: 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('legacy_id_mappings')->insert([
                'table_name' => 'guests',
                'legacy_id' => trim($row[0]),
                'new_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported " . count($results) . " guests.");
    }

    private function importVehicles()
    {
        $this->info("\n--- Importing Vehicles ---");

        $results = $this->querySqlServer("
            SELECT VehicleName, ItemCategoryID
            FROM SCTK_MAST_Vehicles
        ");

        $this->info("Found " . count($results) . " vehicles in legacy database.");

        DB::table('vehicles')->truncate();

        $categoryMappings = DB::table('legacy_id_mappings')
            ->where('table_name', 'item_categories')
            ->pluck('new_id', 'legacy_id')
            ->toArray();

        $bar = $this->output->createProgressBar(count($results));

        foreach ($results as $row) {
            if (count($row) < 2) continue;

            $catLegacyId = trim($row[1]);
            $categoryId = ($catLegacyId && $catLegacyId !== 'NULL') ? ($categoryMappings[$catLegacyId] ?? null) : null;

            DB::table('vehicles')->insert([
                'vehicle_name' => trim($row[0]),
                'item_category_id' => $categoryId,
                'is_active' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported " . count($results) . " vehicles.");
    }

    private function importTickets()
    {
        $this->info("\n--- Importing Tickets ---");
        $this->warn("This will import 2.2M+ tickets and 3.9M+ ticket lines.");
        $this->warn("Estimated time: 30-60 minutes.");

        if (!$this->confirm('Do you want to continue?', true)) {
            return;
        }

        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('ticket_lines')->truncate();
        DB::table('tickets')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get mappings
        $itemMappings = DB::table('legacy_id_mappings')
            ->where('table_name', 'item_rates')
            ->pluck('new_id', 'legacy_id')
            ->toArray();

        $guestMappings = DB::table('legacy_id_mappings')
            ->where('table_name', 'guests')
            ->pluck('new_id', 'legacy_id')
            ->toArray();

        // Get item names
        $itemNames = DB::table('item_rates')->pluck('item_name', 'id')->toArray();

        // Process by date to manage memory
        $dateResults = $this->querySqlServer("
            SELECT DISTINCT CONVERT(VARCHAR(10), TicketDate, 120) as TicketDate
            FROM SCTK_TRANS_TicketHdr
            ORDER BY TicketDate
        ");

        $this->info("Processing " . count($dateResults) . " unique dates...");

        $totalTickets = 0;
        $totalLines = 0;

        $bar = $this->output->createProgressBar(count($dateResults));

        foreach ($dateResults as $dateRow) {
            $date = trim($dateRow[0]);
            if (empty($date)) continue;

            // Get tickets for this date
            $tickets = $this->querySqlServer("
                SELECT BranchID, CONVERT(VARCHAR(10), TicketDate, 120) as TicketDate,
                       TicketNo, PaymentModeID, CONVERT(VARCHAR(19), FerryTime, 120) as FerryTime,
                       FerryBoatID, FerryType, TotalItemLevy, TotalAmount,
                       DiscountPercent, DiscountAmount, NetAmount, ReceivedAmount,
                       BalanceAmount, PendingAmount, GuestID, CustomerID,
                       DestBranchID, DestBranchName, NoOfUnits
                FROM SCTK_TRANS_TicketHdr
                WHERE CONVERT(VARCHAR(10), TicketDate, 120) = '{$date}'
            ");

            foreach ($tickets as $ticket) {
                if (count($ticket) < 20) continue;

                $guestLegacyId = trim($ticket[15]);
                $guestId = ($guestLegacyId && $guestLegacyId !== 'NULL') ? ($guestMappings[$guestLegacyId] ?? null) : null;

                $ticketId = DB::table('tickets')->insertGetId([
                    'ticket_date' => trim($ticket[1]) ?: null,
                    'ticket_no' => (int)trim($ticket[2]),
                    'branch_id' => (int)trim($ticket[0]),
                    'dest_branch_id' => (int)trim($ticket[17]) ?: null,
                    'dest_branch_name' => trim($ticket[18] ?? ''),
                    'ferry_boat_id' => (int)trim($ticket[5]),
                    'payment_mode' => $this->getPaymentMode((int)trim($ticket[3])),
                    'ferry_time' => trim($ticket[4]) ?: null,
                    'ferry_type' => trim($ticket[6] ?? ''),
                    'discount_pct' => (float)trim($ticket[9]) ?: 0,
                    'discount_rs' => (float)trim($ticket[10]) ?: 0,
                    'total_amount' => (float)trim($ticket[8]) ?: 0,
                    'total_levy' => (float)trim($ticket[7]) ?: 0,
                    'net_amount' => (float)trim($ticket[11]) ?: 0,
                    'received_amount' => is_numeric(trim($ticket[12])) ? (float)trim($ticket[12]) : null,
                    'balance_amount' => is_numeric(trim($ticket[13])) ? (float)trim($ticket[13]) : null,
                    'pending_amount' => is_numeric(trim($ticket[14])) ? (float)trim($ticket[14]) : null,
                    'no_of_units' => (int)trim($ticket[19]) ?: null,
                    'guest_id' => $guestId,
                    'customer_id' => is_numeric(trim($ticket[16])) ? (int)trim($ticket[16]) : null,
                    'user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalTickets++;

                // Get ticket lines
                $lines = $this->querySqlServer("
                    SELECT BranchID, CONVERT(VARCHAR(10), TicketDate, 120) as TicketDate,
                           TicketNo, ItemID, ItemRate, Quantity, ItemLevy,
                           ItemSurchargeP, LevySurchargeP, Amount, VehicleNo, VehicleName
                    FROM SCTK_TRANS_TicketFtr
                    WHERE BranchID = " . (int)trim($ticket[0]) . "
                      AND CONVERT(VARCHAR(10), TicketDate, 120) = '{$date}'
                      AND TicketNo = " . (int)trim($ticket[2]) . "
                ");

                $lineInserts = [];
                foreach ($lines as $line) {
                    if (count($line) < 12) continue;

                    $itemLegacyId = trim($line[3]);
                    $itemId = $itemMappings[$itemLegacyId] ?? null;
                    $itemName = $itemId ? ($itemNames[$itemId] ?? 'Unknown') : 'Unknown';

                    $lineInserts[] = [
                        'ticket_id' => $ticketId,
                        'branch_id' => (int)trim($line[0]),
                        'ticket_date' => trim($line[1]) ?: null,
                        'ticket_no' => (int)trim($line[2]),
                        'item_id' => $itemId,
                        'item_name' => $itemName,
                        'qty' => (int)trim($line[5]) ?: 1,
                        'rate' => (float)trim($line[4]) ?: 0,
                        'levy' => (float)trim($line[6]) ?: 0,
                        'surcharge_pct' => (float)trim($line[7]) ?: 0,
                        'levy_surcharge_pct' => (float)trim($line[8]) ?: 0,
                        'amount' => (float)trim($line[9]) ?: 0,
                        'vehicle_no' => trim($line[10] ?? ''),
                        'vehicle_name' => trim($line[11] ?? ''),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $totalLines++;
                }

                if (!empty($lineInserts)) {
                    DB::table('ticket_lines')->insert($lineInserts);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported {$totalTickets} tickets and {$totalLines} ticket lines.");
    }

    private function getPaymentMode($paymentModeId)
    {
        $modes = [
            1 => 'Cash',
            2 => 'Card',
            3 => 'UPI',
            4 => 'Credit',
        ];

        return $modes[$paymentModeId] ?? 'Cash';
    }
}
