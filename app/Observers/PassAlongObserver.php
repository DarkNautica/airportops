<?php

namespace App\Observers;

use App\Models\PassAlong;
use RuntimeException;

class PassAlongObserver
{
    public function updating(PassAlong $passAlong): void
    {
        if ($passAlong->isImmutable()) {
            throw new RuntimeException('Pass Along is locked/submitted and cannot be modified.');
        }
    }

    public function deleting(PassAlong $passAlong): void
    {
        if ($passAlong->isImmutable()) {
            throw new RuntimeException('Pass Along is locked/submitted and cannot be deleted.');
        }
    }
}
