<?php

namespace App\Filament\Audit\Resources\AuditProgramResource\Pages;

use App\Filament\Audit\Resources\AuditProgramResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListAuditPrograms extends ListRecords
{
    protected static string $resource = AuditProgramResource::class;

    protected function getHeaderActions(): array
    {
        // Task 7: Export Strategy
        // ITJEN: Export CSV restricted
        // BPK: Export CSV/PDF full

        $user = auth()->user();
        $isBpk = $user->isBpk();

        return [
            Actions\Action::make('export_audit')
                ->label($isBpk ? 'Export Official Audit (CSV)' : 'Export Internal (CSV)')
                ->action(fn () => $this->exportCsv($user))
                ->requiresConfirmation(),
        ];
    }

    public function exportCsv($user)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit-programs-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($user) {
            $file = fopen('php://output', 'w');

            // Watermark
            $watermark = $user->isBpk() ? 'OFFICIAL STATE AUDIT' : 'FOR INTERNAL AUDIT';
            fputcsv($file, [$watermark]);
            fputcsv($file, ['Auditor: ' . $user->name, 'Timestamp: ' . now()]);
            if ($user->isBpk()) {
                fputcsv($file, ['Hash: ' . hash('sha256', now())]); // Simple hash simulation
            }
            fputcsv($file, []);

            fputcsv($file, ['ID', 'Program', 'Status', 'Last Update']);

            \App\Models\Program::chunk(100, function ($programs) use ($file) {
                foreach ($programs as $p) {
                    fputcsv($file, [$p->id, $p->nama_program, $p->status->value, $p->updated_at]);
                }
            });

            fclose($file);

            // Log export
             app(\App\Services\AuditService::class)->log(
                'audit.export_generated',
                'Program',
                0,
                ['actor_type' => 'AUDITOR', 'details' => 'Exported program list']
            );
        };

        return response()->stream($callback, 200, $headers);
    }
}
