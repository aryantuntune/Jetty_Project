<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log a security event
     *
     * @param string $event Event name (login, logout, verify_ticket, payment, etc.)
     * @param string $action Action result (success, failure, attempt)
     * @param string|null $description Human-readable description
     * @param array $metadata Additional contextual data
     * @param string $severity Severity level (info, warning, critical)
     * @param mixed $auditable Related model instance
     * @return AuditLog
     */
    public static function log(
        string $event,
        string $action,
        ?string $description = null,
        array $metadata = [],
        string $severity = 'info',
        $auditable = null,
        $user = null
    ): AuditLog {
        // Get user if not provided
        if (!$user) {
            $user = auth()->user();
        }

        // Build audit data
        $data = [
            'event' => $event,
            'action' => $action,
            'description' => $description,
            'severity' => $severity,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
        ];

        // Add user information
        if ($user) {
            $data['user_id'] = $user->id;
            $data['user_type'] = get_class($user);
            $data['user_name'] = $user->name ?? $user->email;
        }

        // Add auditable information
        if ($auditable) {
            $data['auditable_type'] = get_class($auditable);
            $data['auditable_id'] = $auditable->id;
        }

        return AuditLog::create($data);
    }

    /**
     * Log a login attempt
     */
    public static function loginAttempt(string $email, bool $success, $user = null): AuditLog
    {
        return self::log(
            event: 'login',
            action: $success ? 'success' : 'failure',
            description: $success ? "User logged in: {$email}" : "Failed login attempt: {$email}",
            metadata: ['email' => $email],
            severity: $success ? 'info' : 'warning',
            user: $user
        );
    }

    /**
     * Log a ticket verification
     */
    public static function ticketVerification($ticket, $checker, bool $success): AuditLog
    {
        return self::log(
            event: 'verify_ticket',
            action: $success ? 'success' : 'failure',
            description: $success
                ? "Ticket #{$ticket->id} verified by {$checker->name}"
                : "Failed to verify ticket #{$ticket->id}",
            metadata: [
                'ticket_id' => $ticket->id,
                'checker_id' => $checker->id,
                'branch_id' => $checker->branch_id ?? null,
            ],
            severity: 'info',
            auditable: $ticket,
            user: $checker
        );
    }

    /**
     * Log a payment transaction
     */
    public static function payment($booking, string $paymentId, bool $verified): AuditLog
    {
        return self::log(
            event: 'payment',
            action: $verified ? 'verified' : 'failed',
            description: $verified
                ? "Payment verified: {$paymentId}"
                : "Payment verification failed: {$paymentId}",
            metadata: [
                'payment_id' => $paymentId,
                'booking_id' => $booking->id ?? null,
                'amount' => $booking->total_amount ?? null,
            ],
            severity: $verified ? 'info' : 'warning',
            auditable: $booking
        );
    }

    /**
     * Log an authorization failure
     */
    public static function authorizationFailure(string $resource, string $action, $user = null): AuditLog
    {
        return self::log(
            event: 'authorization',
            action: 'failure',
            description: "Unauthorized attempt to {$action} {$resource}",
            metadata: [
                'resource' => $resource,
                'attempted_action' => $action,
            ],
            severity: 'warning',
            user: $user
        );
    }

    /**
     * Log a security event (suspicious activity)
     */
    public static function securityEvent(string $description, array $metadata = []): AuditLog
    {
        return self::log(
            event: 'security',
            action: 'detected',
            description: $description,
            metadata: $metadata,
            severity: 'critical'
        );
    }
}
