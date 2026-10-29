<?php

namespace Filament\Tests\Upgrade\Fixtures\TypedCharts;

use Filament\Widgets\BarChartWidget as LegacyBar;
use Filament\Widgets as Widgets;

class Sales extends LegacyBar {}

class Bubbles extends \Filament\Widgets\BubbleChartWidget {}

class Doughnut extends Widgets\DoughnutChartWidget {}

abstract class Line extends \Filament\Widgets\LineChartWidget {}

class Pie extends \Filament\Widgets\PieChartWidget {}

class Polar extends \Filament\Widgets\PolarAreaChartWidget {}

class Radar extends \Filament\Widgets\RadarChartWidget {}

class Scatter extends \Filament\Widgets\ScatterChartWidget {}

class CustomType extends LegacyBar
{
    public function getType(): string
    {
        return 'line';
    }
}

class Child extends Sales
{
    protected function getType(): string
    {
        return parent::getType();
    }
}

class Unrelated extends \stdClass {}

$anonymous = new class extends \Filament\Widgets\PieChartWidget {};

return [new Sales, new Bubbles, new Doughnut, new class extends Line {}, new Pie, new Polar, new Radar, new Scatter, new CustomType, new Child, $anonymous];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\TypedCharts;

use Filament\Widgets\ChartWidget;
use stdClass;
use Filament\Widgets\BarChartWidget as LegacyBar;
use Filament\Widgets as Widgets;

class Sales extends ChartWidget
{
    protected function getType(): string
    {
        return 'bar';
    }
}

class Bubbles extends ChartWidget
{
    protected function getType(): string
    {
        return 'bubble';
    }
}

class Doughnut extends ChartWidget
{
    protected function getType(): string
    {
        return 'doughnut';
    }
}

abstract class Line extends ChartWidget
{
    protected function getType(): string
    {
        return 'line';
    }
}

class Pie extends ChartWidget
{
    protected function getType(): string
    {
        return 'pie';
    }
}

class Polar extends ChartWidget
{
    protected function getType(): string
    {
        return 'polarArea';
    }
}

class Radar extends ChartWidget
{
    protected function getType(): string
    {
        return 'radar';
    }
}

class Scatter extends ChartWidget
{
    protected function getType(): string
    {
        return 'scatter';
    }
}

class CustomType extends ChartWidget
{
    public function getType(): string
    {
        return 'line';
    }
}

class Child extends Sales
{
    protected function getType(): string
    {
        return parent::getType();
    }
}

class Unrelated extends stdClass {}

$anonymous = new class extends ChartWidget
{
    protected function getType(): string
    {
        return 'pie';
    }
};

return [new Sales, new Bubbles, new Doughnut, new class extends Line {}, new Pie, new Polar, new Radar, new Scatter, new CustomType, new Child, $anonymous];
