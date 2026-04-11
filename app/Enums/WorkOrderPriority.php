<?php

namespace App\Enums;

final class WorkOrderPriority
{
    public const LOW = 'Low';
    public const NORMAL = 'Normal';
    public const HIGH = 'High';
    public const CRITICAL = 'Critical';

    public static function all(): array
    {
        return [
            self::LOW,
            self::NORMAL,
            self::HIGH,
            self::CRITICAL,
        ];
    }
}
