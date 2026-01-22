<?php

namespace App\Filament\Widgets;

use App\Models\Program;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProgramBySektorChart extends ChartWidget
{
    protected static ?string $heading = 'Program per Sektor';

    protected function getData(): array
    {
        $data = Program::select('sektor', DB::raw('count(*) as total'))
            ->groupBy('sektor')
            ->pluck('total', 'sektor')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Program',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
