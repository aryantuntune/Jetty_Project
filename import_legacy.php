<?php

/**
 * Direct Legacy Data Import Script
 * Run this script directly: php import_legacy.php [table]
 * Tables: branches, ferryboats, schedules, categories, items, guests, vehicles, tickets, all
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$table = $argv[1] ?? 'all';

$sqlcmdPath = 'C:\\Program Files (x86)\\Microsoft SQL Server\\100\\Tools\\Binn\\sqlcmd.exe';
$tempDir = __DIR__ . '/storage/app/legacy_import';

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

echo "Starting legacy data import...\n";

function querySqlServer($sql, $sqlcmdPath, $tempDir) {
    $tempFile = $tempDir . '/query_result_' . time() . '.txt';

    // Write query to a temp file to avoid escaping issues
    $queryFile = $tempDir . '/query_' . time() . '.sql';
    file_put_contents($queryFile, $sql);

    $cmd = "\"$sqlcmdPath\" -S \"(local)\\SQLEXPRESS\" -E -d MTMS_e -i \"$queryFile\" -s \"|\" -W -h -1 -o \"$tempFile\" 2>&1";

    exec($cmd, $output, $returnCode);

    if (file_exists($queryFile)) {
        unlink($queryFile);
    }

    if (!file_exists($tempFile)) {
        echo "Error: Output file not created. Return code: $returnCode\n";
        return [];
    }

    $content = file_get_contents($tempFile);
    unlink($tempFile);

    $lines = explode("\n", trim($content));

    $results = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, 'rows affected') !== false || strpos($line, 'Msg ') === 0) {
            continue;
        }
        $results[] = explode('|', $line);
    }

    return $results;
}

// ==================== IMPORT FUNCTIONS ====================

function importBranches($sqlcmdPath, $tempDir) {
    echo "\n--- Importing Branches ---\n";

    $results = querySqlServer("
        SELECT BranchID, BranchName, BranchAddress, BranchPhone,
               DestBranchID, DestBranchName, FerryBoatID
        FROM SCTK_MAST_Branches
        WHERE BranchID <> 99
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " branches in legacy database.\n";

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('branches')->truncate();
    DB::table('legacy_id_mappings')->where('table_name', 'branches')->delete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

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

        echo ".";
    }

    echo "\nImported " . count($results) . " branches.\n";
}

function importFerryboats($sqlcmdPath, $tempDir) {
    echo "\n--- Importing Ferryboats ---\n";

    $results = querySqlServer("
        SELECT FerryBoatID, FerryBoatNo, FerryBoatName, activeflag
        FROM SCTK_MAST_FerryBoats
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " ferryboats in legacy database.\n";

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('ferryboats')->truncate();
    DB::table('legacy_id_mappings')->where('table_name', 'ferryboats')->delete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

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

        echo ".";
    }

    echo "\nImported " . count($results) . " ferryboats.\n";
}

function importSchedules($sqlcmdPath, $tempDir) {
    echo "\n--- Importing Ferry Schedules ---\n";

    $results = querySqlServer("
        SELECT FerryScheduleID, FerryScheduleHour, FerryScheduleMinute,
               CONVERT(VARCHAR(8), FerryScheduleTime, 108) as ScheduleTime, activeflag
        FROM SCTK_MAST_FerrySchedules
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " schedules in legacy database.\n";

    DB::table('ferry_schedules')->truncate();
    DB::table('legacy_id_mappings')->where('table_name', 'ferry_schedules')->delete();

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

        echo ".";
    }

    echo "\nImported " . count($results) . " ferry schedules.\n";
}

function importCategories($sqlcmdPath, $tempDir) {
    echo "\n--- Importing Item Categories ---\n";

    $results = querySqlServer("
        SELECT ItemCategoryID, ItemCategoryName, ItemCategoryLevy, activeflag
        FROM SCTK_MAST_ItemCategories
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " item categories in legacy database.\n";

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('item_categories')->truncate();
    DB::table('legacy_id_mappings')->where('table_name', 'item_categories')->delete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

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

        echo ".";
    }

    echo "\nImported " . count($results) . " item categories.\n";

    // Guest Categories
    echo "\n--- Importing Guest Categories ---\n";

    $results = querySqlServer("
        SELECT GuestCategoryID, GuestCategoryName, activeflag
        FROM SCTK_MAST_GuestCategories
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " guest categories in legacy database.\n";

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('guest_categories')->truncate();
    DB::table('legacy_id_mappings')->where('table_name', 'guest_categories')->delete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

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

        echo ".";
    }

    echo "\nImported " . count($results) . " guest categories.\n";
}

function importItems($sqlcmdPath, $tempDir) {
    echo "\n--- Importing Item Rates ---\n";

    $results = querySqlServer("
        SELECT ItemID, ItemCategoryID, ItemName, ItemShortName,
               ItemRate, ItemLevy, ItemSurchargeP, LevySurchargeP,
               SpaceUnits, IsFixedRate, IsVehicle, activeflag
        FROM SCTK_MAST_Items
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " items in legacy database.\n";

    DB::table('item_rates')->truncate();
    DB::table('legacy_id_mappings')->where('table_name', 'item_rates')->delete();

    $categoryMappings = DB::table('legacy_id_mappings')
        ->where('table_name', 'item_categories')
        ->pluck('new_id', 'legacy_id')
        ->toArray();

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

        echo ".";
    }

    echo "\nImported " . count($results) . " item rates.\n";
}

function importGuests($sqlcmdPath, $tempDir) {
    echo "\n--- Importing Guests ---\n";

    $results = querySqlServer("
        SELECT GuestID, GuestCategoryID, GuestName, GuestAddress,
               GuestPhone, GuestDesignation, GuestRemark, activeflag
        FROM SCTK_MAST_Guests
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " guests in legacy database.\n";

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('guests')->truncate();
    DB::table('legacy_id_mappings')->where('table_name', 'guests')->delete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $categoryMappings = DB::table('legacy_id_mappings')
        ->where('table_name', 'guest_categories')
        ->pluck('new_id', 'legacy_id')
        ->toArray();

    $firstBranch = DB::table('branches')->first();

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
            'branch_id' => $firstBranch->id ?? 1,
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

        echo ".";
    }

    echo "\nImported " . count($results) . " guests.\n";
}

function importVehicles($sqlcmdPath, $tempDir) {
    echo "\n--- Importing Vehicles ---\n";

    $results = querySqlServer("
        SELECT VehicleName, ItemCategoryID
        FROM SCTK_MAST_Vehicles
    ", $sqlcmdPath, $tempDir);

    echo "Found " . count($results) . " vehicles in legacy database.\n";

    DB::table('vehicles')->truncate();

    $categoryMappings = DB::table('legacy_id_mappings')
        ->where('table_name', 'item_categories')
        ->pluck('new_id', 'legacy_id')
        ->toArray();

    $count = 0;
    foreach ($results as $row) {
        if (count($row) < 2) continue;

        $catLegacyId = trim($row[1]);
        $categoryId = ($catLegacyId && $catLegacyId !== 'NULL' && $catLegacyId !== '')
            ? ($categoryMappings[$catLegacyId] ?? null)
            : null;

        DB::table('vehicles')->insert([
            'vehicle_name' => trim($row[0]),
            'item_category_id' => $categoryId,
            'is_active' => 'Y',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $count++;
        if ($count % 100 == 0) echo ".";
    }

    echo "\nImported " . count($results) . " vehicles.\n";
}

function importTickets($sqlcmdPath, $tempDir, $autoConfirm = false) {
    global $errorLogFile;

    echo "\n--- Importing Tickets ---\n";
    echo "WARNING: This will import 2.2M+ tickets and 3.9M+ ticket lines.\n";
    echo "Estimated time: 30-60 minutes.\n";

    if (!$autoConfirm) {
        echo "Press Enter to continue or Ctrl+C to cancel...\n";
        fgets(STDIN);
    } else {
        echo "Auto-confirm enabled. Starting import...\n";
    }

    // Set up error logging
    $errorLogFile = __DIR__ . '/storage/logs/tickets_import_errors_' . date('Ymd_His') . '.json';
    $failedTickets = [];
    $failedLines = [];
    $skippedDates = [];

    echo "Error log will be saved to: $errorLogFile\n\n";

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
    $dateResults = querySqlServer("
        SELECT DISTINCT CONVERT(VARCHAR(10), TicketDate, 120) as TicketDate
        FROM SCTK_TRANS_TicketHdr
        ORDER BY 1
    ", $sqlcmdPath, $tempDir);

    echo "Processing " . count($dateResults) . " unique dates...\n";

    $totalTickets = 0;
    $totalLines = 0;
    $totalErrors = 0;
    $dateCount = 0;

    foreach ($dateResults as $dateRow) {
        $date = trim($dateRow[0]);
        if (empty($date)) continue;

        $dateCount++;
        $dateErrors = 0;

        try {
            // Get tickets for this date
            $tickets = querySqlServer("
                SELECT BranchID, CONVERT(VARCHAR(10), TicketDate, 120) as TicketDate,
                       TicketNo, PaymentModeID, CONVERT(VARCHAR(19), FerryTime, 120) as FerryTime,
                       FerryBoatID, FerryType, TotalItemLevy, TotalAmount,
                       DiscountPercent, DiscountAmount, NetAmount, ReceivedAmount,
                       BalanceAmount, PendingAmount, GuestID, CustomerID,
                       DestBranchID, DestBranchName, NoOfUnits
                FROM SCTK_TRANS_TicketHdr
                WHERE CONVERT(VARCHAR(10), TicketDate, 120) = '$date'
            ", $sqlcmdPath, $tempDir);

            if (empty($tickets)) {
                $skippedDates[] = ['date' => $date, 'reason' => 'No tickets found'];
                echo "[$dateCount/" . count($dateResults) . "] $date: SKIPPED (no tickets)\n";
                continue;
            }

            // Get ticket lines for this date in one query
            $allLines = querySqlServer("
                SELECT BranchID, CONVERT(VARCHAR(10), TicketDate, 120) as TicketDate,
                       TicketNo, ItemID, ItemRate, Quantity, ItemLevy,
                       ItemSurchargeP, LevySurchargeP, Amount, VehicleNo, VehicleName
                FROM SCTK_TRANS_TicketFtr
                WHERE CONVERT(VARCHAR(10), TicketDate, 120) = '$date'
            ", $sqlcmdPath, $tempDir);

            // Group lines by ticket
            $linesGrouped = [];
            foreach ($allLines as $line) {
                if (count($line) < 12) continue;
                $key = trim($line[0]) . '_' . trim($line[2]); // BranchID_TicketNo
                $linesGrouped[$key][] = $line;
            }

            // Process tickets one by one for better error handling
            foreach ($tickets as $ticket) {
                if (count($ticket) < 20) {
                    $failedTickets[] = [
                        'date' => $date,
                        'raw_data' => $ticket,
                        'error' => 'Incomplete ticket data (less than 20 columns)',
                    ];
                    $dateErrors++;
                    continue;
                }

                try {
                    $guestLegacyId = trim($ticket[15]);
                    $guestId = ($guestLegacyId && $guestLegacyId !== 'NULL' && $guestLegacyId !== '')
                        ? ($guestMappings[$guestLegacyId] ?? null)
                        : null;

                    $ticketData = [
                        'ticket_date' => trim($ticket[1]) ?: null,
                        'ticket_no' => (int)trim($ticket[2]),
                        'branch_id' => (int)trim($ticket[0]),
                        'dest_branch_id' => (int)trim($ticket[17]) ?: null,
                        'dest_branch_name' => trim($ticket[18] ?? ''),
                        'ferry_boat_id' => (int)trim($ticket[5]),
                        'payment_mode' => getPaymentMode((int)trim($ticket[3])),
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
                    ];

                    $ticketId = DB::table('tickets')->insertGetId($ticketData);
                    $totalTickets++;

                    // Insert ticket lines for this ticket
                    $key = trim($ticket[0]) . '_' . trim($ticket[2]);
                    if (isset($linesGrouped[$key])) {
                        foreach ($linesGrouped[$key] as $line) {
                            try {
                                $itemLegacyId = trim($line[3]);
                                $itemId = ($itemLegacyId && $itemLegacyId !== 'NULL') ? ($itemMappings[$itemLegacyId] ?? null) : null;
                                $itemName = $itemId ? ($itemNames[$itemId] ?? 'Unknown') : 'Unknown';

                                DB::table('ticket_lines')->insert([
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
                                ]);

                                $totalLines++;
                            } catch (\Exception $e) {
                                $failedLines[] = [
                                    'ticket_id' => $ticketId,
                                    'branch_id' => trim($line[0]),
                                    'ticket_date' => $date,
                                    'ticket_no' => trim($line[2]),
                                    'raw_data' => $line,
                                    'error' => $e->getMessage(),
                                ];
                                $dateErrors++;
                            }
                        }
                    }

                } catch (\Exception $e) {
                    $failedTickets[] = [
                        'branch_id' => trim($ticket[0]),
                        'date' => $date,
                        'ticket_no' => trim($ticket[2]),
                        'raw_data' => $ticket,
                        'error' => $e->getMessage(),
                    ];
                    $dateErrors++;
                }
            }

            $totalErrors += $dateErrors;
            $errorIndicator = $dateErrors > 0 ? " [ERRORS: $dateErrors]" : "";
            echo "[$dateCount/" . count($dateResults) . "] $date: " . count($tickets) . " tickets$errorIndicator\n";

        } catch (\Exception $e) {
            $skippedDates[] = [
                'date' => $date,
                'reason' => 'Exception: ' . $e->getMessage(),
            ];
            echo "[$dateCount/" . count($dateResults) . "] $date: FAILED - " . $e->getMessage() . "\n";
        }

        // Save error log periodically (every 50 dates)
        if ($dateCount % 50 == 0) {
            saveErrorLog($errorLogFile, $failedTickets, $failedLines, $skippedDates, $totalTickets, $totalLines, $totalErrors);
        }
    }

    // Final error log save
    saveErrorLog($errorLogFile, $failedTickets, $failedLines, $skippedDates, $totalTickets, $totalLines, $totalErrors);

    echo "\n";
    echo "==========================================\n";
    echo "IMPORT SUMMARY\n";
    echo "==========================================\n";
    echo "Total tickets imported: $totalTickets\n";
    echo "Total ticket lines imported: $totalLines\n";
    echo "Total errors: $totalErrors\n";
    echo "Failed tickets: " . count($failedTickets) . "\n";
    echo "Failed lines: " . count($failedLines) . "\n";
    echo "Skipped dates: " . count($skippedDates) . "\n";
    echo "==========================================\n";

    if ($totalErrors > 0) {
        echo "\nError details saved to: $errorLogFile\n";
        echo "You can review and re-import failed tickets later.\n";
    }
}

function saveErrorLog($file, $failedTickets, $failedLines, $skippedDates, $totalTickets, $totalLines, $totalErrors) {
    $data = [
        'generated_at' => date('Y-m-d H:i:s'),
        'summary' => [
            'total_tickets_imported' => $totalTickets,
            'total_lines_imported' => $totalLines,
            'total_errors' => $totalErrors,
            'failed_tickets_count' => count($failedTickets),
            'failed_lines_count' => count($failedLines),
            'skipped_dates_count' => count($skippedDates),
        ],
        'failed_tickets' => $failedTickets,
        'failed_lines' => $failedLines,
        'skipped_dates' => $skippedDates,
    ];

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function getPaymentMode($paymentModeId) {
    $modes = [
        1 => 'Cash',
        2 => 'Card',
        3 => 'UPI',
        4 => 'Credit',
    ];

    return $modes[$paymentModeId] ?? 'Cash';
}

// ==================== MAIN EXECUTION ====================

$autoConfirm = in_array('--auto', $argv);

$tables = $table === 'all'
    ? ['branches', 'ferryboats', 'schedules', 'categories', 'items', 'guests', 'vehicles', 'tickets']
    : [$table];

foreach ($tables as $t) {
    $function = 'import' . ucfirst($t);
    if (function_exists($function)) {
        if ($t === 'tickets') {
            $function($sqlcmdPath, $tempDir, $autoConfirm);
        } else {
            $function($sqlcmdPath, $tempDir);
        }
    } else {
        echo "Unknown table: $t\n";
    }
}

echo "\n\nImport completed!\n";
