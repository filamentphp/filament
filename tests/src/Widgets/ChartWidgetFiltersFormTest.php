<?php

namespace Filament\Tests\Widgets;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersForm;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Livewire\Livewire;

uses(TestCase::class);

dataset('chart filters forms', [
    'canonical trait and configuration' => [TestChartWidgetWithFiltersForm::class],
    'canonical trait and legacy configuration' => [TestChartWidgetWithLegacyFiltersSchema::class],
    'legacy trait and configuration' => [TestLegacyChartWidgetWithFiltersSchema::class],
    'legacy trait and canonical configuration' => [TestLegacyChartWidgetWithFiltersForm::class],
]);

it('initializes and renders filters once using either trait and configuration hook', function (string $widgetClass): void {
    Livewire::test($widgetClass)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('filterHydrations', 1)
        ->assertSeeHtml('id="filters.year"')
        ->set('filters.year', '2023')
        ->call('resetFilters')
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('filterHydrations', 2);
})->with('chart filters forms');

it('preserves the cached `filtersSchema` key, defaults and getter aliases', function (string $widgetClass): void {
    $widget = Livewire::test($widgetClass, ['cacheFiltersForm' => true]);
    $instance = $widget->instance();
    $schema = $instance->getSchema('filtersSchema');

    expect($schema)
        ->toBe($instance->getFiltersForm())
        ->toBe($instance->getFiltersSchema())
        ->and($schema->getKey())->toBe('filtersSchema')
        ->and($schema->getStatePath())->toBe('filters')
        ->and($schema->getColumns('lg'))->toBe(3)
        ->and($instance->getCachedSchemas())->toHaveKeys(['filtersSchema'])
        ->and($instance->discoveredSchemaNames)->toContain('filtersSchema');

    // A subsequent Livewire request rebuilds the discovered schema cache.
    $widget->set('filters.year', '2023')
        ->assertSeeHtml('id="filtersSchema.year"')
        ->call('resetFilters')
        ->assertSet('filters', ['year' => '2024']);

    expect($widget->instance()->getFiltersForm()->getKey())->toBe('filtersSchema');
})->with('chart filters forms');

it('preserves legacy and canonical getter overrides calling `parent::`', function (string $widgetClass): void {
    $widget = Livewire::test($widgetClass)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSeeHtml('id="filters.year"');

    expect($widget->instance()->getFiltersForm()->getColumns('lg'))->toBe(4)
        ->and($widget->instance()->getSchema('filtersSchema')->getColumns('lg'))->toBe(4);
})->with([
    'canonical' => [TestChartWidgetWithFiltersFormGetter::class],
    'legacy' => [TestChartWidgetWithLegacyFiltersSchemaGetter::class],
]);

it('does not expose filter configuration or getter methods to Livewire', function (string $method): void {
    Livewire::test(TestLegacyChartWidgetWithFiltersSchema::class)
        ->call($method)
        ->assertForbidden();
})->with(['filtersForm', 'filtersSchema', 'getFiltersForm', 'getFiltersSchema']);

abstract class TestChartWidgetFiltersFormBase extends ChartWidget
{
    public int $filterHydrations = 0;

    public function mount(bool $cacheFiltersForm = false): void
    {
        parent::mount();

        if ($cacheFiltersForm) {
            $this->getSchema('filtersSchema');
        }
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function configureFiltersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('year')
                ->options(['2024' => '2024', '2023' => '2023'])
                ->default('2024')
                ->afterStateHydrated(function (): void {
                    $this->filterHydrations++;
                }),
        ]);
    }

    protected function defaultFiltersSchema(Schema $schema): Schema
    {
        return $schema->columns(3);
    }
}

abstract class TestChartWidgetFiltersForm extends TestChartWidgetFiltersFormBase
{
    use HasFiltersForm;
}

class TestChartWidgetWithFiltersForm extends TestChartWidgetFiltersForm
{
    public function filtersForm(Schema $schema): Schema
    {
        return $this->configureFiltersForm(parent::filtersForm($schema));
    }
}

class TestChartWidgetWithLegacyFiltersSchema extends TestChartWidgetFiltersForm
{
    public function filtersSchema(Schema $schema): Schema
    {
        return $this->configureFiltersForm(parent::filtersSchema($schema));
    }
}

abstract class TestLegacyChartWidgetFiltersSchema extends TestChartWidgetFiltersFormBase
{
    use HasFiltersSchema;
}

class TestLegacyChartWidgetWithFiltersSchema extends TestLegacyChartWidgetFiltersSchema
{
    public function filtersSchema(Schema $schema): Schema
    {
        return $this->configureFiltersForm(parent::filtersSchema($schema));
    }
}

class TestLegacyChartWidgetWithFiltersForm extends TestLegacyChartWidgetFiltersSchema
{
    public function filtersForm(Schema $schema): Schema
    {
        return $this->configureFiltersForm(parent::filtersForm($schema));
    }
}

class TestChartWidgetWithFiltersFormGetter extends TestChartWidgetWithFiltersForm
{
    public function getFiltersForm(): Schema
    {
        return parent::getFiltersForm()->columns(4);
    }
}

class TestChartWidgetWithLegacyFiltersSchemaGetter extends TestLegacyChartWidgetWithFiltersSchema
{
    public function getFiltersSchema(): Schema
    {
        return parent::getFiltersSchema()->columns(4);
    }
}
