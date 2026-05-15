<?php

namespace Filament\Tests\Widgets;

use Filament\Tests\TestCase;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('returns `true` from `canView()` by default', function (): void {
    expect(TestWidget::canView())->toBeTrue();
});

it('returns `-1` from `getSort()` when `$sort` is not set', function (): void {
    expect(TestWidget::getSort())->toBe(-1);
});

it('returns the configured sort order from `getSort()` when `$sort` is overridden', function (): void {
    expect(TestWidgetWithSort::getSort())->toBe(5);
});

it('returns `true` from `isDiscovered()` by default', function (): void {
    expect(TestWidget::isDiscovered())->toBeTrue();
});

it('returns `false` from `isDiscovered()` when `$isDiscovered` is overridden', function (): void {
    expect(TestWidgetNotDiscovered::isDiscovered())->toBeFalse();
});

it('returns default column span of `1` from `getColumnSpan()`', function (): void {
    $widget = app(TestWidget::class);

    expect($widget->getColumnSpan())->toBe(1);
});

it('returns overridden column span from `getColumnSpan()`', function (): void {
    $widget = app(TestWidgetWithColumnSpan::class);

    expect($widget->getColumnSpan())->toBe('full');
});

it('returns empty column start from `getColumnStart()` by default', function (): void {
    $widget = app(TestWidget::class);

    expect($widget->getColumnStart())->toBe([]);
});

it('returns overridden column start from `getColumnStart()`', function (): void {
    $widget = app(TestWidgetWithColumnStart::class);

    expect($widget->getColumnStart())->toBe(2);
});

it('returns `WidgetConfiguration` from `make()`', function (): void {
    $configuration = TestWidget::make();

    expect($configuration)->toBeInstanceOf(WidgetConfiguration::class);
    expect($configuration->widget)->toBe(TestWidget::class);
});

it('passes properties to `WidgetConfiguration` via `make()`', function (): void {
    $configuration = TestWidget::make(['foo' => 'bar']);

    expect($configuration->getProperties())->toBe(['foo' => 'bar']);
});

it('returns placeholder data from `getPlaceholderData()`', function (): void {
    $widget = app(TestWidget::class);

    expect($widget->getPlaceholderData())
        ->toBe([
            'columnSpan' => 1,
            'columnStart' => [],
        ]);
});

it('returns `[\'lazy\' => true]` from `getDefaultProperties()` when `$isLazy` is `true`', function (): void {
    expect(TestWidget::getDefaultProperties())->toBe(['lazy' => true]);
});

it('returns empty array from `getDefaultProperties()` when `$isLazy` is `false`', function (): void {
    expect(TestWidgetNotLazy::getDefaultProperties())->toBe([]);
});

it('re-authorizes the widget on Livewire updates after the initial mount', function (): void {
    AuthorizableTestWidget::$canViewFlag = true;

    $component = livewire(AuthorizableTestWidget::class);

    AuthorizableTestWidget::$canViewFlag = false;

    $component
        ->set('name', 'foo')
        ->assertStatus(403);

    AuthorizableTestWidget::$canViewFlag = true;
});

class TestWidget extends Widget
{
    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetWithSort extends Widget
{
    protected static ?int $sort = 5;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetNotDiscovered extends Widget
{
    protected static bool $isDiscovered = false;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetWithColumnSpan extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetWithColumnStart extends Widget
{
    protected int | string | array $columnStart = 2;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetNotLazy extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class AuthorizableTestWidget extends Widget
{
    public static bool $canViewFlag = true;

    public ?string $name = null;

    protected string $view = 'pages.settings';

    public static function canView(): bool
    {
        return static::$canViewFlag;
    }

    protected function getViewData(): array
    {
        return [];
    }
}
