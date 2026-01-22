<?php

namespace App\Services;

use App\Enums\ProgramStatus;
use App\Models\Program;
use App\Models\ProgramStatusHistory;
use App\Support\WorkflowGuard;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class WorkflowService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    /**
     * Advance the status of the program to the next logical step.
     *
     * @param Program $program
     * @return Program
     */
    public function advanceStatus(Program $program): Program
    {
        return DB::transaction(function () use ($program) {
            $currentStatus = $program->status;
            $nextStatus = $this->getNextStatus($currentStatus);

            $oldStatusValue = $currentStatus->value;
            $newStatusValue = $nextStatus->value;

            // Use WorkflowGuard to bypass the Observer restriction
            WorkflowGuard::runAsWorkflow(function () use ($program, $nextStatus) {
                $program->update(['status' => $nextStatus]);
            });

            $this->auditService->logModel('program.status_advanced', $program, [
                'from_status' => $oldStatusValue,
                'to_status' => $newStatusValue,
                'user_id' => Auth::id(),
            ]);

            // Task 4: Status History (State Transition Log)
            $user = Auth::user();
            ProgramStatusHistory::create([
                'program_id' => $program->id,
                'from_status' => $oldStatusValue,
                'to_status' => $newStatusValue,
                'changed_by' => $user?->id,
                'changed_by_role' => $user?->role?->value,
                'changed_at' => now(),
            ]);

            return $program;
        });
    }

    /**
     * Determine the next status based on the strict linear workflow.
     *
     * @param ProgramStatus $currentStatus
     * @return ProgramStatus
     */
    private function getNextStatus(ProgramStatus $currentStatus): ProgramStatus
    {
        return match ($currentStatus) {
            ProgramStatus::TERDAFTAR => ProgramStatus::DIBAHAS_PU,
            ProgramStatus::DIBAHAS_PU => ProgramStatus::CATATAN_KL,
            ProgramStatus::CATATAN_KL => ProgramStatus::KONSOLIDASI_PEMDA,
            ProgramStatus::KONSOLIDASI_PEMDA => ProgramStatus::BERITA_ACARA,
            ProgramStatus::BERITA_ACARA => throw new DomainException("Program sudah mencapai tahap akhir (BERITA_ACARA)."),
        };
    }
}
