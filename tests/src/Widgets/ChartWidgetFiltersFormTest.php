<?php

namespace Filament\Tests\Widgets;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersForm;
use Livewire\Livewire;

uses(TestCase::class);

it('initializes and renders filters once', function (): void {
    Livewire::test(TestChartWidgetWithFiltersForm::class)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('filterHydrations', 1)
        ->assertSeeHtml('id="filters.year"')
        ->set('filters.year', '2023')
        ->call('resetFilters')
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('filterHydrations', 2);
});

it('preserves the cached `filtersSchema` key and defaults', function (): void {
    $widget = Livewire::test(TestChartWidgetWithFiltersForm::class, ['cacheFiltersForm' => true]);
    $instance = $widget->instance();
    $schema = $instance->getSchema('filtersSchema');

    expect($schema)
        ->toBe($instance->getFiltersForm())
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
});

it('uses `getFiltersForm()` overrides calling `parent::`', function (): void {
    $widget = Livewire::test(TestChartWidgetWithFiltersFormGetter::class)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSeeHtml('id="filters.year"');

    expect($widget->instance()->getFiltersForm()->getColumns('lg'))->toBe(4)
        ->and($widget->instance()->getSchema('filtersSchema')->getColumns('lg'))->toBe(4);
});

it('does not expose filter configuration or getter methods to Livewire', function (string $method): void {
    Livewire::test(TestChartWidgetWithFiltersForm::class)
        ->call($method)
        ->assertForbidden();
})->with(['filtersForm', 'getFiltersForm']);

abstract class TestChartWidgetFiltersFormBase extends ChartWidget
{
    use HasFiltersForm;

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

class TestChartWidgetWithFiltersForm extends TestChartWidgetFiltersFormBase
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
