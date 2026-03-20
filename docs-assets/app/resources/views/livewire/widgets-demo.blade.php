<div class="min-h-screen bg-gray-50 p-8 dark:bg-gray-950">
    <div id="statsSimple" class="p-16 max-w-5xl">
        @livewire(\App\Filament\Widgets\StatsOverviewSimple::class)
    </div>

    <div id="statsDescription" class="p-16 max-w-5xl">
        @livewire(\App\Filament\Widgets\StatsOverviewDescription::class)
    </div>

    <div id="statsColor" class="p-16 max-w-5xl">
        @livewire(\App\Filament\Widgets\StatsOverviewColor::class)
    </div>

    <div id="statsChart" class="p-16 max-w-5xl">
        @livewire(\App\Filament\Widgets\StatsOverviewChart::class)
    </div>

    <div id="statsHeading" class="p-16 max-w-5xl">
        @livewire(\App\Filament\Widgets\StatsOverviewHeading::class)
    </div>

    <div id="chartLine" class="p-16 max-w-3xl">
        @livewire(\App\Filament\Widgets\LineChartDemo::class)
    </div>

    <div id="chartBar" class="p-16 max-w-3xl">
        @livewire(\App\Filament\Widgets\BarChartDemo::class)
    </div>

    <div id="chartDescription" class="p-16 max-w-3xl">
        @livewire(\App\Filament\Widgets\ChartWithDescriptionDemo::class)
    </div>

    <div id="chartFilter" class="p-16 max-w-3xl">
        @livewire(\App\Filament\Widgets\ChartWithFilterDemo::class)
    </div>
</div>
