<?php

namespace App\Enums;

final class WorkOrderStatus
{
    public const OPEN = 'Open';
    public const IN_PROGRESS = 'In Progress';
    public const ON_HOLD = 'On Hold';
    public const COMPLETED = 'Completed';
    public const CLOSED = 'Closed';

    public static function all(): array
    {
        return [
            self::OPEN,
            self::IN_PROGRESS,
            self::ON_HOLD,
            self::COMPLETED,
            self::CLOSED,
        ];
    }
}
