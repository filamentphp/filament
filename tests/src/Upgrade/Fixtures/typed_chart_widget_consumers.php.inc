<?php

namespace Filament\Tests\Upgrade\Fixtures\TypedChartConsumers;

use Filament\Widgets\BarChartWidget;
use Filament\Widgets\LineChartWidget;

trait Data
{
    protected function getData(): array
    {
        return ['datasets' => [['data' => [3, 5]]], 'labels' => ['April', 'May']];
    }
}

trait CustomType
{
    protected function getType(): string
    {
        return 'radar';
    }
}

trait NestedType
{
    use CustomType;
}

trait NamedType
{
    protected function chartType(): string
    {
        return 'scatter';
    }
}

trait RequiresType
{
    abstract protected function getType(): string;
}

class Sales extends BarChartWidget
{
    use Data;
}

class MonthlySales extends Sales
{
    protected function getType(): string
    {
        return parent::getType();
    }
}

class SharedDataLine extends LineChartWidget
{
    use Data;
}

class TraitBar extends BarChartWidget
{
    use NestedType;
}

class TraitLine extends LineChartWidget
{
    use CustomType;
}

class Unrelated
{
    use CustomType;
}

class Adapted extends BarChartWidget
{
    use NamedType { chartType as getType; }
}

class Required extends BarChartWidget
{
    use RequiresType;
}

class ClassOverride extends BarChartWidget
{
    use CustomType;

    protected function getType(): string
    {
        return parent::getType() === 'bar' ? 'pie' : 'line';
    }
}

class DescendantOverride extends Sales
{
    use CustomType;
}

class ParentRead extends LineChartWidget
{
    public function originalType(): string
    {
        return parent::GETTYPE();
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}

class NestedClass extends BarChartWidget
{
    public function nestedType(): string
    {
        $widget = new class extends MonthlySales
        {
            public function getType(): string
            {
                return parent::getType();
            }
        };

        return $widget->getType();
    }
}

return [new Sales, new MonthlySales, new SharedDataLine, new TraitBar, new TraitLine, new Unrelated, new Adapted, new Required, new ClassOverride, new DescendantOverride, new ParentRead, new NestedClass];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\TypedChartConsumers;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\BarChartWidget;
use Filament\Widgets\LineChartWidget;

trait Data
{
    protected function getData(): array
    {
        return ['datasets' => [['data' => [3, 5]]], 'labels' => ['April', 'May']];
    }
}

trait CustomType
{
    protected function getType(): string
    {
        return 'radar';
    }
}

trait NestedType
{
    use CustomType;
}

trait NamedType
{
    protected function chartType(): string
    {
        return 'scatter';
    }
}

trait RequiresType
{
    abstract protected function getType(): string;
}

class Sales extends ChartWidget
{
    use Data;
    protected function getType(): string
    {
        return 'bar';
    }
}

class MonthlySales extends Sales
{
    protected function getType(): string
    {
        return parent::getType();
    }
}

class SharedDataLine extends ChartWidget
{
    use Data;
    protected function getType(): string
    {
        return 'line';
    }
}

class TraitBar extends ChartWidget
{
    use NestedType;
}

class TraitLine extends ChartWidget
{
    use CustomType;
}

class Unrelated
{
    use CustomType;
}

class Adapted extends ChartWidget
{
    use NamedType { chartType as getType; }
}

class Required extends ChartWidget
{
    use RequiresType;
    protected function getType(): string
    {
        return 'bar';
    }
}

class ClassOverride extends ChartWidget
{
    use CustomType;

    protected function getType(): string
    {
        return 'bar' === 'bar' ? 'pie' : 'line';
    }
}

class DescendantOverride extends Sales
{
    use CustomType;
}

class ParentRead extends ChartWidget
{
    public function originalType(): string
    {
        return 'line';
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}

class NestedClass extends ChartWidget
{
    public function nestedType(): string
    {
        $widget = new class extends MonthlySales
        {
            public function getType(): string
            {
                return parent::getType();
            }
        };

        return $widget->getType();
    }
    protected function getType(): string
    {
        return 'bar';
    }
}

return [new Sales, new MonthlySales, new SharedDataLine, new TraitBar, new TraitLine, new Unrelated, new Adapted, new Required, new ClassOverride, new DescendantOverride, new ParentRead, new NestedClass];
