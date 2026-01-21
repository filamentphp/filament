<?php

namespace App\Observers;

use App\Models\Program;
use App\Support\WorkflowGuard;
use Exception;

class ProgramObserver
{
    /**
     * Handle the Program "updating" event.
     */
    public function updating(Program $program): void
    {
        if ($program->isDirty('status')) {
            if (! WorkflowGuard::isWorkflowAction()) {
                throw new Exception('Perubahan status program hanya boleh dilakukan melalui WorkflowService.');
            }
        }
    }
}
