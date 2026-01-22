<?php

namespace App\Filament\Audit\Resources\AuditProgramResource\Pages;

use App\Filament\Audit\Resources\AuditProgramResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;

class ViewAuditProgram extends ViewRecord
{
    protected static string $resource = AuditProgramResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Program Details')
                    ->schema([
                        TextEntry::make('nama_program'),
                        TextEntry::make('sektor'),
                        TextEntry::make('lokasi'),
                        TextEntry::make('estimasi_biaya')->money('IDR'),
                        TextEntry::make('status')->badge(),
                    ]),
                // Task 1: View Catatan & BA
                Section::make('Riwayat Catatan')
                    ->schema([
                        RepeatableEntry::make('catatans')
                            ->schema([
                                TextEntry::make('tahap'),
                                TextEntry::make('sumber'),
                                TextEntry::make('isi_catatan'),
                                TextEntry::make('created_at'),
                            ])
                    ]),
                Section::make('Berita Acara')
                    ->schema([
                        TextEntry::make('beritaAcara.keputusan'),
                        TextEntry::make('beritaAcara.tanggal'),
                    ])
                    ->visible(fn ($record) => $record->beritaAcara !== null),
            ]);
    }
}
