<?php

// app/Models/Ticket.php
namespace App\Models;

use App\Models\TicketLine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Boot method to auto-generate qr_hash on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            // Generate cryptographically secure hash for QR code
            if (empty($ticket->qr_hash)) {
                $ticket->qr_hash = $ticket->generateQrHash();
            }
        });
    }

    /**
     * Generate secure QR hash for this ticket
     * Uses ticket data + secret key to create non-enumerable hash
     *
     * @return string 64-character SHA-256 hash
     */
    public function generateQrHash(): string
    {
        // Get QR secret from config (should be set in .env)
        $secret = config('app.qr_secret', config('app.key'));

        // Combine multiple fields to create unique, non-guessable hash
        $data = implode('|', [
            Str::uuid()->toString(),           // Random UUID for uniqueness
            microtime(true),                   // High precision timestamp
            $secret,                           // Secret key from env
            $this->id ?? 0,                    // Ticket ID (if available)
            $this->branch_id ?? 0,             // Branch context
            $this->booking_id ?? 0,            // Booking context
        ]);

        return hash('sha256', $data);
    }

    /**
     * Verify if a QR hash matches this ticket
     *
     * @param string $hash The hash to verify
     * @return bool True if hash matches
     */
    public function verifyQrHash(string $hash): bool
    {
        return hash_equals($this->qr_hash, $hash);
    }

    protected $fillable = [
        // Legacy composite key fields
        'ticket_date',
        'ticket_no',
        'branch_id',
        'dest_branch_id',
        'dest_branch_name',
        'ferry_boat_id',
        'payment_mode',
        'ferry_time',
        'ferry_type',
        // Financial fields
        'discount_pct',
        'discount_rs',
        'total_amount',
        'total_levy',
        'total_surcharge',
        'net_amount',
        'received_amount',
        'balance_amount',
        'pending_amount',
        // Additional info
        'no_of_units',
        'user_id',
        'customer_name',
        'customer_mobile',
        'guest_id',
        'customer_id',
        'verified_at',
        'checker_id',
        'created_by',
        'updated_by',
        // Mobile app fields
        'source',
        'payment_id',
        'qr_code',
        'qr_hash',  // Secure QR code hash
        'status',
    ];

    protected $casts = [
        'ticket_date' => 'date',
        'ferry_time' => 'datetime',
        'verified_at' => 'datetime',
        'discount_pct' => 'decimal:2',
        'discount_rs' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_levy' => 'decimal:2',
        'total_surcharge' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
    ];

    // Relationships
    public function lines()
    {
        return $this->hasMany(TicketLine::class);
    }

    public function ferryBoat()
    {
        return $this->belongsTo(FerryBoat::class, 'ferry_boat_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function destBranch()
    {
        return $this->belongsTo(Branch::class, 'dest_branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Scopes for common queries
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('ticket_date', $date);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('ticket_date', [$startDate, $endDate]);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('ticket_date', $year)
            ->whereMonth('ticket_date', $month);
    }

    // Helper to get legacy-style ticket reference
    public function getLegacyReferenceAttribute()
    {
        return $this->branch_id . '-' . $this->ticket_date->format('Ymd') . '-' . $this->ticket_no;
    }
}
