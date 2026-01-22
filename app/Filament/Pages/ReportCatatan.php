<?php

namespace App\Filament\Pages;

use App\Models\Catatan;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ReportCatatan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Catatan Konsolidasi';
    protected static string $view = 'filament.pages.report-catatan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Catatan::query()->with('program') // Eager load
            )
            ->columns([
                Tables\Columns\TextColumn::make('program.nama_program')->label('Program')->searchable(),
                Tables\Columns\TextColumn::make('tahap')->sortable(),
                Tables\Columns\TextColumn::make('sumber')->sortable(),
                Tables\Columns\TextColumn::make('isi_catatan')->limit(50)->label('Ringkasan'),
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('tahap')
                    ->options([
                        'DIBAHAS_PU' => 'DIBAHAS_PU',
                        'CATATAN_KL' => 'CATATAN_KL',
                        'KONSOLIDASI_PEMDA' => 'KONSOLIDASI_PEMDA',
                    ]),
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
            'Content-Disposition' => 'attachment; filename="laporan-catatan-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Laporan Catatan Konsolidasi - READ ONLY SYSTEM OUTPUT']);
            fputcsv($file, ['Dicetak Oleh: ' . auth()->user()->name, 'Tanggal: ' . now()]);
            fputcsv($file, []);
            fputcsv($file, ['Program', 'Tahap', 'Sumber', 'Isi Catatan', 'Waktu']);

            Catatan::query()
                ->with('program')
                ->chunk(100, function ($catatans) use ($file) {
                    foreach ($catatans as $catatan) {
                        fputcsv($file, [
                            $catatan->program->nama_program,
                            $catatan->tahap,
                            $catatan->sumber,
                            $catatan->isi_catatan,
                            $catatan->created_at,
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
