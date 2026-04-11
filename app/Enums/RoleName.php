<?php

namespace App\Enums;

final class RoleName
{
    public const ADMIN = 'Admin';
    public const OPS_SUPERVISOR = 'Ops Supervisor';
    public const OPS_STAFF = 'Ops Staff';
    public const VIEWER = 'Viewer';

    public static function all(): array
    {
        return [self::ADMIN, self::OPS_SUPERVISOR, self::OPS_STAFF, self::VIEWER];
    }
}
