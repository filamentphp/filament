<?php

use Filament\Infolists\View\Components\IconEntryComponent\IconComponent as InfolistIconEntryIconComponent;
use Filament\Infolists\View\Components\TextEntryComponent\ItemComponent as IconTextEntryItemComponent;
use Filament\Infolists\View\Components\TextEntryComponent\ItemComponent\IconComponent as InfolistTextEntryItemIconComponent;
use Filament\Notifications\View\Components\NotificationComponent;
use Filament\Notifications\View\Components\NotificationComponent\IconComponent as NotificationIconComponent;
use Filament\Schemas\View\Components\IconComponent as SchemaIconComponent;
use Filament\Support\Colors\Color;
use Filament\Support\Colors\ColorManager;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\ButtonComponent;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\DropdownComponent\HeaderComponent as DropdownHeaderComponent;
use Filament\Support\View\Components\DropdownComponent\ItemComponent as DropdownItemComponent;
use Filament\Support\View\Components\DropdownComponent\ItemComponent\IconComponent as DropdownItemIconComponent;
use Filament\Support\View\Components\IconButtonComponent;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent as InputWrapperIconComponent;
use Filament\Support\View\Components\LinkComponent;
use Filament\Support\View\Components\ModalComponent\IconComponent as ModalIconComponent;
use Filament\Support\View\Components\SectionComponent\IconComponent as SectionIconComponent;
use Filament\Support\View\Components\ToggleComponent;
use Filament\Tables\View\Components\Columns\IconColumnComponent\IconComponent as TableIconColumnIconComponent;
use Filament\Tables\View\Components\Columns\Summarizers\CountComponent\IconComponent as TableColumnCountSummarizerIconComponent;
use Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent as TableTextColumnItemComponent;
use Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent\IconComponent as TableTextColumnItemIconComponent;
use Filament\Tests\TestCase;
use Filament\Widgets\View\Components\ChartWidgetComponent;
use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\DescriptionComponent as StatsOverviewWidgetStatDescriptionComponent;
use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\StatsOverviewWidgetStatChartComponent;
use Illuminate\Support\Str;

uses(TestCase::class);

it('generates colors from a HEX value', function (string $color): void {
    expect(Color::generatePalette($color))
        ->toMatchSnapshot();
})->with([
    '#49D359',
    '#8A2BE2',
    '#A52A2A',
    '#000000',
    '#FFFFFF',
]);

it('generates colors from an RGB value', function (string $color): void {
    expect(Color::generatePalette($color))
        ->toMatchSnapshot();
})->with([
    'rgb(128, 8, 8)',
    'rgb(93, 255, 2)',
    'rgb(243, 243, 21)',
    'rgb(0, 0, 0)',
    'rgb(255, 255, 255)',
]);

it('returns all colors', function (): void {
    $colors = [];

    foreach ((new ReflectionClass(Color::class))->getConstants() as $name => $color) {
        $colors[Str::lower($name)] = $color;
    }

    expect(Color::all())
        ->toBe($colors);
});

it('generates component classes', function (string | HasColor $component, string $color): void {
    expect(FilamentColor::getComponentClasses($component, $color))
        ->toMatchSnapshot();
})
    ->with([
        'badge' => BadgeComponent::class,
        'button' => new ButtonComponent(isOutlined: false),
        'outlined button' => new ButtonComponent(isOutlined: true),
        'chart widget' => ChartWidgetComponent::class,
        'dropdown header' => DropdownHeaderComponent::class,
        'dropdown item icon' => DropdownItemIconComponent::class,
        'dropdown item' => DropdownItemComponent::class,
        'icon button' => IconButtonComponent::class,
        'infolist icon entry icon' => InfolistIconEntryIconComponent::class,
        'infolist text entry item' => IconTextEntryItemComponent::class,
        'infolist text entry item icon' => InfolistTextEntryItemIconComponent::class,
        'input wrapper icon' => InputWrapperIconComponent::class,
        'link' => LinkComponent::class,
        'modal icon' => ModalIconComponent::class,
        'notification' => NotificationComponent::class,
        'notification icon' => NotificationIconComponent::class,
        'schema icon' => SchemaIconComponent::class,
        'section icon' => SectionIconComponent::class,
        'stats overview widget stat description' => StatsOverviewWidgetStatDescriptionComponent::class,
        'stats overview widget stat chart' => StatsOverviewWidgetStatChartComponent::class,
        'table column count summarizer icon' => TableColumnCountSummarizerIconComponent::class,
        'table icon column icon' => TableIconColumnIconComponent::class,
        'table text column item' => TableTextColumnItemComponent::class,
        'table text column item icon' => TableTextColumnItemIconComponent::class,
        'toggle' => ToggleComponent::class,
    ])
    ->with(fn (): array => array_keys(app(ColorManager::class)->getColors()));
