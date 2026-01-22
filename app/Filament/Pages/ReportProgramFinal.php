<?php

namespace App\Filament\Pages;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportProgramFinal extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Program Final';
    protected static string $view = 'filament.pages.report-program-final';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Program::query()
                    ->where('status', ProgramStatus::BERITA_ACARA)
                    ->with('beritaAcara') // Eager load
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_program')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sektor')->sortable(),
                Tables\Columns\TextColumn::make('lokasi')->searchable(),
                Tables\Columns\TextColumn::make('beritaAcara.keputusan')->label('Keputusan')->badge(),
                Tables\Columns\TextColumn::make('beritaAcara.tanggal')->label('Tanggal BA')->date(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('Export CSV')
                    ->action(fn () => $this->exportCsv()),
            ]);
    }

    public function exportCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-program-final-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Laporan Program Final - READ ONLY SYSTEM OUTPUT']);
            fputcsv($file, ['Dicetak Oleh: ' . auth()->user()->name, 'Tanggal: ' . now()]);
            fputcsv($file, []);
            fputcsv($file, ['Nama Program', 'Sektor', 'Lokasi', 'Keputusan', 'Tanggal BA']);

            Program::query()
                ->where('status', ProgramStatus::BERITA_ACARA)
                ->with('beritaAcara')
                ->chunk(100, function ($programs) use ($file) {
                    foreach ($programs as $program) {
                        fputcsv($file, [
                            $program->nama_program,
                            $program->sektor,
                            $program->lokasi,
                            $program->beritaAcara?->keputusan ?? '-',
                            $program->beritaAcara?->tanggal ?? '-',
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
