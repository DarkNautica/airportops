<?php

namespace App\Enums;

final class RoleName
{
    public const ADMIN = 'Admin';
    public const OPS_SUPERVISOR = 'Ops Supervisor';
    public const OPS_TECH = 'Ops Tech';

    public static function all(): array
    {
        return [self::ADMIN, self::OPS_SUPERVISOR, self::OPS_TECH];
    }
}
