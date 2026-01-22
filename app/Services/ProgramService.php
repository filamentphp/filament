<?php

namespace App\Services;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ProgramService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    /**
     * Create a new program.
     *
     * @param array $data
     * @return Program
     */
    public function createProgram(array $data): Program
    {
        return DB::transaction(function () use ($data) {
            // Force status to initial state
            $data['status'] = ProgramStatus::TERDAFTAR;
            $data['created_by'] = Auth::id();

            $program = Program::create($data);

            $this->auditService->logModel('program.created', $program, [
                'new' => $program->toArray(),
            ]);

            return $program;
        });
    }

    /**
     * Update an existing program.
     *
     * @param Program $program
     * @param array $data
     * @return Program
     */
    public function updateProgram(Program $program, array $data): Program
    {
        // Status must not be updated here
        if (isset($data['status'])) {
            unset($data['status']);
        }

        return DB::transaction(function () use ($program, $data) {
            $oldData = $program->toArray();

            $program->update($data);

            $this->auditService->logModel('program.updated', $program, [
                'old' => $oldData,
                'new' => $program->toArray(),
            ]);

            return $program;
        });
    }
}
