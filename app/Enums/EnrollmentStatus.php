<?php

namespace App\Enums;

/**
 * Enrollment Status Constants
 * Used to standardize enrollment status across the application
 */
class EnrollmentStatus
{
    public const ACTIVE = 'active';
    public const COMPLETED = 'completed';
    public const PAID = 'paid';
    public const PENDING = 'pending';
    public const CANCELLED = 'cancelled';
    public const EXPIRED = 'expired';

    /**
     * Get all valid statuses that grant access to course materials
     */
    public static function accessGranted(): array
    {
        return [
            self::ACTIVE,
            self::COMPLETED,
            self::PAID,
        ];
    }

    /**
     * Get all valid statuses
     */
    public static function all(): array
    {
        return [
            self::ACTIVE,
            self::COMPLETED,
            self::PAID,
            self::PENDING,
            self::CANCELLED,
            self::EXPIRED,
        ];
    }
}
