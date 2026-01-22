<?php

namespace App\Services;

use App\Models\Catatan;
use App\Models\Program;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class CatatanService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    /**
     * Add a new note (Catatan) to the program.
     *
     * @param Program $program
     * @param array $data
     * @return Catatan
     */
    public function addCatatan(Program $program, array $data): Catatan
    {
        // Check if the input 'tahap' matches the current program status
        // We assume $data['tahap'] comes as a string matching the Enum value or just the string value
        $inputTahap = $data['tahap'] ?? null;

        if ($inputTahap !== $program->status->value) {
            throw new DomainException("Tahap catatan ({$inputTahap}) tidak sesuai dengan status program saat ini ({$program->status->value}).");
        }

        return DB::transaction(function () use ($program, $data) {
            $data['program_id'] = $program->id;
            $data['dicatat_oleh'] = Auth::id();

            $catatan = Catatan::create($data);

            $this->auditService->logModel('catatan.created', $catatan, [
                'new' => $catatan->toArray(),
            ]);

            return $catatan;
        });
    }
}
