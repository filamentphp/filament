<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;

class Dashboard extends Component
{
    public function render()
    {
        return view('filament::.dashboard', [
            'chart' => (new ColumnChartModel())
                            ->setTitle('Sample Chart')
                            ->addColumn('Column 1', 100, '#f6ad55')
                            ->addColumn('Column 2', 200, '#fc8181')
                            ->addColumn('Column 3', 300, '#90cdf4'),

        ])->layout('filament::components.layouts.app');
    }
}
