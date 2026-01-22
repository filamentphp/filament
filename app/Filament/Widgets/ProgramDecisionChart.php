<?php

namespace App\Filament\Widgets;

use App\Models\BeritaAcara;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProgramDecisionChart extends ChartWidget
{
    protected static ?string $heading = 'Keputusan Final (Berita Acara)';

    protected function getData(): array
    {
        $data = BeritaAcara::select('keputusan', DB::raw('count(*) as total'))
            ->groupBy('keputusan')
            ->pluck('total', 'keputusan')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Keputusan',
                    'data' => array_values($data),
                    'backgroundColor' => ['#22c55e', '#ef4444'], // Green, Red
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
