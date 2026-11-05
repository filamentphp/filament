<?php

namespace Filament\Tests\Upgrade\Fixtures\FormNaming;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class StandardChart extends ChartWidget
{
    use HasFiltersSchema;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('year')->options(['2024' => '2024', '2023' => '2023'])->default('2024'),
        ]);
    }

    protected function getType(): string
    {
        return 'line';
    }
}

abstract class BaseChart extends ChartWidget
{
    use HasFiltersSchema;
}

class ParentChart extends BaseChart
{
    public int $configurations = 0;

    public function filtersSchema(Schema $schema): Schema
    {
        $this->configurations++;

        return parent::filtersSchema($schema)->components([
            Select::make('year')->options(['2024' => '2024', '2023' => '2023'])->default('2024'),
        ]);
    }

    protected function getType(): string
    {
        return 'line';
    }
}

class ChildChart extends ParentChart
{
    public function filtersSchema(Schema $schema): Schema
    {
        return parent::filtersSchema(schema: $schema)->columns(3);
    }
}
