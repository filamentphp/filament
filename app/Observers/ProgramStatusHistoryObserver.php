<?php

namespace App\Observers;

use App\Models\ProgramStatusHistory;
use Exception;

class ProgramStatusHistoryObserver
{
    public function updating(ProgramStatusHistory $history): void
    {
        throw new Exception("History is IMMUTABLE. Update denied.");
    }

    public function deleting(ProgramStatusHistory $history): void
    {
        throw new Exception("History is IMMUTABLE. Delete denied.");
    }
}
