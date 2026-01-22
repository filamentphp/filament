<?php

namespace App\Observers;

use App\Models\AuditLog;
use Exception;

class AuditLogObserver
{
    public function updating(AuditLog $auditLog): void
    {
        throw new Exception("Audit Log is IMMUTABLE. Update denied.");
    }

    public function deleting(AuditLog $auditLog): void
    {
        throw new Exception("Audit Log is IMMUTABLE. Delete denied.");
    }
}
