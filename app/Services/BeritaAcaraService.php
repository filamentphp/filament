<?php

namespace App\Services;

use App\Enums\ProgramStatus;
use App\Models\BeritaAcara;
use App\Models\Program;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class BeritaAcaraService
{
    public function __construct(
        private readonly WorkflowService $workflowService,
        private readonly AuditService $auditService
    ) {}

    /**
     * Finalize the program by uploading Berita Acara and advancing status.
     *
     * @param Program $program
     * @param UploadedFile $file
     * @param array $data
     * @return BeritaAcara
     */
    public function finalizeProgram(Program $program, UploadedFile $file, array $data): BeritaAcara
    {
        if ($program->status !== ProgramStatus::KONSOLIDASI_PEMDA) {
            throw new DomainException("Finalisasi program hanya dapat dilakukan saat status KONSOLIDASI_PEMDA.");
        }

        return DB::transaction(function () use ($program, $file, $data) {
            // Upload file to secure storage (private)
            $path = $file->store('berita_acaras', 'local'); // 'local' or 'private' disk configured in filesystems

            $beritaAcara = BeritaAcara::create([
                'program_id' => $program->id,
                'keputusan' => $data['keputusan'],
                'ringkasan_kesepakatan' => $data['ringkasan_kesepakatan'],
                'tanggal' => $data['tanggal'],
                'file_pdf' => $path,
                'dibuat_oleh' => Auth::id(),
            ]);

            $this->auditService->logModel('berita_acara.created', $beritaAcara, [
                'file_path' => $path
            ]);

            // Advance status to BERITA_ACARA
            $this->workflowService->advanceStatus($program);

            $this->auditService->logModel('program.finalized', $program);

            return $beritaAcara;
        });
    }
}
