<?php

namespace App\Observers;

use App\Enums\ProgramStatus;
use App\Exceptions\WorkflowViolationException;
use App\Models\Program;
use App\Support\WorkflowGuard;

class ProgramObserver
{
    /**
     * Handle the Program "creating" event.
     */
    public function creating(Program $program): void
    {
        if (! isset($program->status)) {
            $program->status = ProgramStatus::TERDAFTAR;
        }

        if ($program->status !== ProgramStatus::TERDAFTAR) {
            throw new WorkflowViolationException('Program baru harus berstatus TERDAFTAR.');
        }
    }

    /**
     * Handle the Program "updating" event.
     */
    public function updating(Program $program): void
    {
        if ($program->isDirty('status')) {
            if (! WorkflowGuard::isWorkflowAction()) {
                throw new WorkflowViolationException('Perubahan status program hanya boleh dilakukan melalui WorkflowService.');
            }
        }
    }
}
