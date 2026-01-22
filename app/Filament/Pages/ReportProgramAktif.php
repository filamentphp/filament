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
use Filament\Actions\Action;
use Illuminate\Support\Facades\Response;

class ReportProgramAktif extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Program Aktif';
    protected static string $view = 'filament.pages.report-program-aktif';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Program::query()->where('status', '!=', ProgramStatus::BERITA_ACARA)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_program')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sektor')->sortable(),
                Tables\Columns\TextColumn::make('lokasi')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')->dateTime(),
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
            'Content-Disposition' => 'attachment; filename="laporan-program-aktif-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Laporan Program Aktif - READ ONLY SYSTEM OUTPUT']);
            fputcsv($file, ['Dicetak Oleh: ' . auth()->user()->name, 'Tanggal: ' . now()]);
            fputcsv($file, []); // Empty line
            fputcsv($file, ['Nama Program', 'Sektor', 'Lokasi', 'Status', 'Last Updated']);

            Program::query()
                ->where('status', '!=', ProgramStatus::BERITA_ACARA)
                ->chunk(100, function ($programs) use ($file) {
                    foreach ($programs as $program) {
                        fputcsv($file, [
                            $program->nama_program,
                            $program->sektor,
                            $program->lokasi,
                            $program->status->value,
                            $program->updated_at,
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
