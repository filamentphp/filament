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

it('generates colors from a HEX value using Filament v3\'s algorithm', function (string $color): void {
    expect(Color::generateV3Palette($color))
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

it('generates colors from an RGB value using Filament v3\'s algorithm', function (string $color): void {
    expect(Color::generateV3Palette($color))
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

it('converts a HEX color to OKLCH via `convertToOklch()`', function (): void {
    $result = Color::convertToOklch('#ff0000');

    expect($result)->toStartWith('oklch(');
});

it('passes through an OKLCH color unchanged via `convertToOklch()`', function (): void {
    $oklch = 'oklch(0.637 0.237 25.331)';

    expect(Color::convertToOklch($oklch))->toBe($oklch);
});

it('converts an RGB color to OKLCH via `convertToOklch()`', function (): void {
    $result = Color::convertToOklch('rgb(255, 0, 0)');

    expect($result)->toStartWith('oklch(');
});

it('converts a HEX color to RGB via `convertToRgb()`', function (): void {
    expect(Color::convertToRgb('#ff0000'))->toBe('rgb(255, 0, 0)');
});

it('passes through an RGB color unchanged via `convertToRgb()`', function (): void {
    $rgb = 'rgb(128, 64, 32)';

    expect(Color::convertToRgb($rgb))->toBe($rgb);
});

it('converts an OKLCH color to RGB via `convertToRgb()`', function (): void {
    $result = Color::convertToRgb('oklch(0.637 0.237 25.331)');

    expect($result)->toStartWith('rgb(');
});

it('passes through a HEX color unchanged via `convertToHex()`', function (): void {
    expect(Color::convertToHex('#ff0000'))->toBe('#ff0000');
});

it('converts an RGB color to HEX via `convertToHex()`', function (): void {
    expect(Color::convertToHex('rgb(255, 0, 0)'))->toBe('#ff0000');
});

it('converts an OKLCH color to HEX via `convertToHex()`', function (): void {
    $result = Color::convertToHex('oklch(0.637 0.237 25.331)');

    expect($result)->toMatch('/^#[0-9a-f]{6}$/');
});

it('calculates a contrast ratio greater than 1 via `calculateContrastRatio()`', function (): void {
    $ratio = Color::calculateContrastRatio('#ffffff', '#000000');

    expect($ratio)->toBeGreaterThan(1);
});

it('returns `1.0` contrast ratio for identical colors via `calculateContrastRatio()`', function (): void {
    $ratio = Color::calculateContrastRatio('#ffffff', '#ffffff');

    expect($ratio)->toEqual(1.0);
});

it('identifies black-on-white as text-contrast-accessible via `isTextContrastRatioAccessible()`', function (): void {
    expect(Color::isTextContrastRatioAccessible('#ffffff', '#000000'))->toBeTrue();
});

it('identifies white-on-white as NOT text-contrast-accessible via `isTextContrastRatioAccessible()`', function (): void {
    expect(Color::isTextContrastRatioAccessible('#ffffff', '#ffffff'))->toBeFalse();
});

it('identifies black-on-white as non-text-contrast-accessible via `isNonTextContrastRatioAccessible()`', function (): void {
    expect(Color::isNonTextContrastRatioAccessible('#ffffff', '#000000'))->toBeTrue();
});

it('identifies white-on-white as NOT non-text-contrast-accessible via `isNonTextContrastRatioAccessible()`', function (): void {
    expect(Color::isNonTextContrastRatioAccessible('#ffffff', '#ffffff'))->toBeFalse();
});

it('generates a palette via `hex()` that matches `generatePalette()`', function (): void {
    $color = '#3b82f6';

    expect(Color::hex($color))->toBe(Color::generatePalette($color));
});

it('generates a palette via `rgb()` that matches `generatePalette()`', function (): void {
    $color = 'rgb(59, 130, 246)';

    expect(Color::rgb($color))->toBe(Color::generatePalette($color));
});

it('identifies a light color via `isLight()`', function (): void {
    // White is very light
    expect(Color::isLight('#ffffff'))->toBeTrue();
});

it('identifies a dark color as not light via `isLight()`', function (): void {
    // Black is very dark
    expect(Color::isLight('#000000'))->toBeFalse();
});

it('generates a palette with expected shade keys via `generatePalette()`', function (): void {
    $palette = Color::generatePalette('#3b82f6');

    expect($palette)->toHaveKeys([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950]);
    expect($palette[500])->toBeString();
});

it('calculates maximum contrast ratio between black and white', function (): void {
    $ratio = Color::calculateContrastRatio('#000000', '#ffffff');

    expect($ratio)->toBeGreaterThan(20.0);
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
