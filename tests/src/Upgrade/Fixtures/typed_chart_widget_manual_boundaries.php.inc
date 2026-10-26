<?php

namespace Filament\Tests\Upgrade\Fixtures\TypedChartBoundaries;

use Filament\Widgets\BarChartWidget;
use Filament\Widgets\LineChartWidget as LegacyLine;

trait CustomType
{
    protected function getType(): string
    {
        return 'radar';
    }
}

trait ParentType
{
    protected function getType(): string
    {
        return parent::getType();
    }
}

trait NamedType
{
    protected function chartType(): string
    {
        return 'scatter';
    }
}

class FromTrait extends BarChartWidget
{
    use CustomType;
}

class FromParentTrait extends LegacyLine
{
    use ParentType;
}

class FromAdaptedTrait extends BarChartWidget
{
    use NamedType { chartType as getType; }
}

class FromParent extends LegacyLine
{
    protected function getType(): string
    {
        return parent::GETTYPE();
    }
}

class ParentCallable extends BarChartWidget
{
    protected function getType(): string
    {
        return (parent::getType(...))();
    }
}

class ParentArrayCallback extends BarChartWidget
{
    protected function getType(): string
    {
        return call_user_func(['parent', 'getType']);
    }
}

class ParentClassCallback extends BarChartWidget
{
    protected function getType(): string
    {
        return call_user_func([parent::class, 'getType']);
    }
}

class ParentStringCallback extends BarChartWidget
{
    protected function getType(): string
    {
        return call_user_func('parent::getType');
    }
}

class ParentCallElsewhere extends BarChartWidget
{
    public function originalType(): string
    {
        return parent::getType();
    }
}

class DynamicParent extends BarChartWidget
{
    public function originalType(string $method): string
    {
        return parent::{$method}();
    }
}

class Intermediate extends FromParent {}

class TypedConsumer
{
    public ?BarChartWidget $chart = null;
}

/** @param array<BarChartWidget> $charts */
function accept(BarChartWidget|LegacyLine $widget, array $charts): BarChartWidget|LegacyLine
{
    return $widget;
}

return [
    BarChartWidget::class,
    $widget instanceof LegacyLine,
    new BarChartWidget(),
    'Filament\\Widgets\\BarChartWidget',
];
