<?php

namespace App\Filament\Widgets;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProgramStatusOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Efficient Query: Count by status in one go if possible, or individual counts
        // Individual counts are safer for Enum grouping consistency

        return [
            Stat::make('Total Program', Program::count()),
            Stat::make('Terdaftar', Program::where('status', ProgramStatus::TERDAFTAR)->count())
                ->color('gray'),
            Stat::make('Dibahas PU', Program::where('status', ProgramStatus::DIBAHAS_PU)->count())
                ->color('info'),
            Stat::make('Catatan K/L', Program::where('status', ProgramStatus::CATATAN_KL)->count())
                ->color('warning'),
            Stat::make('Konsolidasi Pemda', Program::where('status', ProgramStatus::KONSOLIDASI_PEMDA)->count())
                ->color('primary'),
            Stat::make('Berita Acara (Final)', Program::where('status', ProgramStatus::BERITA_ACARA)->count())
                ->color('success'),
        ];
    }
}
