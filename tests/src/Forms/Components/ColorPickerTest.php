<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('format', function (): void {
    it('defaults to `hex` format', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getFormat())->toBe('hex');
    });

    it('can set `hex()` format', function (): void {
        $picker = ColorPicker::make('color')
            ->rgb()
            ->hex();

        expect($picker->getFormat())->toBe('hex');
    });

    it('can set `hsl()` format', function (): void {
        $picker = ColorPicker::make('color')
            ->hsl();

        expect($picker->getFormat())->toBe('hsl');
    });

    it('can set `rgb()` format', function (): void {
        $picker = ColorPicker::make('color')
            ->rgb();

        expect($picker->getFormat())->toBe('rgb');
    });

    it('can set `rgba()` format', function (): void {
        $picker = ColorPicker::make('color')
            ->rgba();

        expect($picker->getFormat())->toBe('rgba');
    });

    it('returns fluent `$this` from format helpers', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->hex())->toBe($picker);
        expect($picker->hsl())->toBe($picker);
        expect($picker->rgb())->toBe($picker);
        expect($picker->rgba())->toBe($picker);
        expect($picker->format('hex'))->toBe($picker);
    });

    it('can set custom `format()`', function (): void {
        $picker = ColorPicker::make('color')
            ->format('hsla');

        expect($picker->getFormat())->toBe('hsla');
    });

    it('can set `format()` with a `Closure`', function (): void {
        $picker = ColorPicker::make('color')
            ->format(static fn (): string => 'hsl');

        expect($picker->getFormat())->toBe('hsl');
    });
});

describe('preset colors', function (): void {
    it('returns `null` for `getPresetColors()` by default', function (): void {
        expect(ColorPicker::make('color')->getPresetColors())->toBeNull();
    });

    it('can set `presetColors()`', function (): void {
        expect(ColorPicker::make('color')->presetColors(['#ef4444' => 'Red', '#111827'])->getPresetColors())
            ->toBe(['#ef4444' => 'Red', '#111827']);
    });

    it('can set `presetColors()` with an `Arrayable` or a `Closure`', function (): void {
        expect(ColorPicker::make('color')->presetColors(collect(['#ef4444' => 'Red']))->getPresetColors())->toBe(['#ef4444' => 'Red'])
            ->and(ColorPicker::make('color')->presetColors(fn (): array => ['#3b82f6' => 'Blue'])->getPresetColors())->toBe(['#3b82f6' => 'Blue']);
    });
});

describe('eye dropper', function (): void {
    it('defaults `hasEyeDropper()` to `false`', function (): void {
        expect(ColorPicker::make('color')->hasEyeDropper())->toBeFalse();
    });

    it('can set `eyeDropper()` with a `Closure`', function (): void {
        expect(ColorPicker::make('color')->eyeDropper()->hasEyeDropper())->toBeTrue()
            ->and(ColorPicker::make('color')->eyeDropper(fn (): bool => false)->hasEyeDropper())->toBeFalse();
    });
});

describe('copyable', function (): void {
    it('defaults `isCopyable()` to `false`', function (): void {
        expect(ColorPicker::make('color')->isCopyable())->toBeFalse();
    });

    it('can set `copyable()`', function (): void {
        expect(ColorPicker::make('color')->copyable()->isCopyable())->toBeTrue();
    });
});

describe('placeholder', function (): void {
    it('returns `null` for `getPlaceholder()` by default', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getPlaceholder())->toBeNull();
    });

    it('can set `placeholder()`', function (): void {
        $picker = ColorPicker::make('color')
            ->placeholder('Pick a color...');

        expect($picker->getPlaceholder())->toBe('Pick a color...');
    });

    it('can set `placeholder()` with a `Closure`', function (): void {
        $picker = ColorPicker::make('color')
            ->placeholder(static fn (): string => 'Dynamic placeholder');

        expect($picker->getPlaceholder())->toBe('Dynamic placeholder');
    });

    it('can clear `placeholder()` with `null`', function (): void {
        $picker = ColorPicker::make('color')
            ->placeholder('Pick a color...')
            ->placeholder(null);

        expect($picker->getPlaceholder())->toBeNull();
    });
});

describe('affixes', function (): void {
    it('returns `null` for `getPrefixLabel()` by default', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getPrefixLabel())->toBeNull();
    });

    it('can set `prefix()`', function (): void {
        $picker = ColorPicker::make('color')
            ->prefix('Color:');

        expect($picker->getPrefixLabel())->toBe('Color:');
    });

    it('can set `prefix()` with a `Closure`', function (): void {
        $picker = ColorPicker::make('color')
            ->prefix(static fn (): string => 'Dynamic prefix');

        expect($picker->getPrefixLabel())->toBe('Dynamic prefix');
    });

    it('returns `null` for `getSuffixLabel()` by default', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getSuffixLabel())->toBeNull();
    });

    it('can set `suffix()`', function (): void {
        $picker = ColorPicker::make('color')
            ->suffix('(hex)');

        expect($picker->getSuffixLabel())->toBe('(hex)');
    });

    it('can set `suffix()` with a `Closure`', function (): void {
        $picker = ColorPicker::make('color')
            ->suffix(static fn (): string => 'Dynamic suffix');

        expect($picker->getSuffixLabel())->toBe('Dynamic suffix');
    });

    it('returns `null` for `getPrefixIcon()` by default', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getPrefixIcon())->toBeNull();
    });

    it('can set `prefixIcon()`', function (): void {
        $picker = ColorPicker::make('color')
            ->prefixIcon('heroicon-o-swatch');

        expect($picker->getPrefixIcon())->toBe('heroicon-o-swatch');
    });

    it('can set `prefixIcon()` with a `Closure`', function (): void {
        $picker = ColorPicker::make('color')
            ->prefixIcon(static fn (): string => 'heroicon-o-eye-dropper');

        expect($picker->getPrefixIcon())->toBe('heroicon-o-eye-dropper');
    });

    it('returns `null` for `getSuffixIcon()` by default', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getSuffixIcon())->toBeNull();
    });

    it('can set `suffixIcon()`', function (): void {
        $picker = ColorPicker::make('color')
            ->suffixIcon('heroicon-o-check');

        expect($picker->getSuffixIcon())->toBe('heroicon-o-check');
    });

    it('can set `suffixIcon()` with a `Closure`', function (): void {
        $picker = ColorPicker::make('color')
            ->suffixIcon(static fn (): string => 'heroicon-o-x-mark');

        expect($picker->getSuffixIcon())->toBe('heroicon-o-x-mark');
    });

    it('returns `null` for `getPrefixIconColor()` by default', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getPrefixIconColor())->toBeNull();
    });

    it('can set `prefixIconColor()`', function (): void {
        $picker = ColorPicker::make('color')
            ->prefixIconColor('danger');

        expect($picker->getPrefixIconColor())->toBe('danger');
    });

    it('returns `null` for `getSuffixIconColor()` by default', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->getSuffixIconColor())->toBeNull();
    });

    it('can set `suffixIconColor()`', function (): void {
        $picker = ColorPicker::make('color')
            ->suffixIconColor('success');

        expect($picker->getSuffixIconColor())->toBe('success');
    });

    it('defaults `isPrefixInline()` to `false`', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->isPrefixInline())->toBeFalse();
    });

    it('can set `inlinePrefix()`', function (): void {
        $picker = ColorPicker::make('color')
            ->inlinePrefix();

        expect($picker->isPrefixInline())->toBeTrue();
    });

    it('sets `isPrefixInline()` via `prefix()` `$isInline` parameter', function (): void {
        $picker = ColorPicker::make('color')
            ->prefix('Color:', isInline: true);

        expect($picker->isPrefixInline())->toBeTrue();
    });

    it('defaults `isSuffixInline()` to `false`', function (): void {
        $picker = ColorPicker::make('color');

        expect($picker->isSuffixInline())->toBeFalse();
    });

    it('can set `inlineSuffix()`', function (): void {
        $picker = ColorPicker::make('color')
            ->inlineSuffix();

        expect($picker->isSuffixInline())->toBeTrue();
    });

    it('sets `isSuffixInline()` via `suffix()` `$isInline` parameter', function (): void {
        $picker = ColorPicker::make('color')
            ->suffix('(hex)', isInline: true);

        expect($picker->isSuffixInline())->toBeTrue();
    });
});

describe('state', function (): void {
    it('can set and get state', function (): void {
        livewire(TestComponentWithColorPicker::class)
            ->fillForm(['color' => '#ff0000'])
            ->assertSchemaStateSet(['color' => '#ff0000']);
    });
});

describe('validation', function (): void {
    it('rejects array state by default', function (): void {
        $field = ColorPicker::make('value')->container(Schema::make(Livewire::make()));
        $validator = Validator::make(['value' => ['unexpected']], ['value' => $field->getValidationRules()]);

        expect($validator->fails())->toBeTrue()
            ->and($validator->failed()['value'])->toHaveKey('String');
    });

    it('accepts string and nullable state by default', function (): void {
        $field = ColorPicker::make('value')->container(Schema::make(Livewire::make()));

        foreach (['012345', '', null] as $state) {
            expect(Validator::make(['value' => $state], ['value' => $field->getValidationRules()])->passes())->toBeTrue();
        }
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(TestComponentWithColorPicker::class)
            ->assertSuccessful();
    });

    it('can render with RGB format', function (): void {
        livewire(TestComponentWithRgbColorPicker::class)
            ->assertSuccessful();
    });

    it('can render with RGBA format', function (): void {
        livewire(TestComponentWithRgbaColorPicker::class)
            ->assertSuccessful();
    });

    it('can render with `hsl()` format', function (): void {
        livewire(RenderColorPickerWithHsl::class)
            ->assertSuccessful();
    });

    it('can render with `hex()` format', function (): void {
        livewire(RenderColorPickerWithHex::class)
            ->assertSuccessful();
    });

    it('can render with custom `format()`', function (): void {
        livewire(RenderColorPickerWithCustomFormat::class)
            ->assertSuccessful();
    });

    it('can render with `format()` set via `Closure`', function (): void {
        livewire(RenderColorPickerWithClosureFormat::class)
            ->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderColorPickerWithPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('Pick a color...');
    });

    it('can render with `placeholder()` set via `Closure`', function (): void {
        livewire(RenderColorPickerWithClosurePlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('Dynamic placeholder');
    });

    it('can render with `placeholder()` cleared to `null`', function (): void {
        livewire(RenderColorPickerWithNullPlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `prefix()`', function (): void {
        livewire(RenderColorPickerWithPrefix::class)
            ->assertSuccessful()
            ->assertSeeHtml('Color:');
    });

    it('can render with `prefix()` set via `Closure`', function (): void {
        livewire(RenderColorPickerWithClosurePrefix::class)
            ->assertSuccessful()
            ->assertSeeHtml('Dynamic prefix');
    });

    it('can render with `suffix()`', function (): void {
        livewire(RenderColorPickerWithSuffix::class)
            ->assertSuccessful()
            ->assertSeeHtml('(hex)');
    });

    it('can render with `suffix()` set via `Closure`', function (): void {
        livewire(RenderColorPickerWithClosureSuffix::class)
            ->assertSuccessful()
            ->assertSeeHtml('Dynamic suffix');
    });

    it('can render with `prefixIcon()`', function (): void {
        livewire(RenderColorPickerWithPrefixIcon::class)
            ->assertSuccessful();
    });

    it('can render with `prefixIcon()` set via `Closure`', function (): void {
        livewire(RenderColorPickerWithClosurePrefixIcon::class)
            ->assertSuccessful();
    });

    it('can render with `suffixIcon()`', function (): void {
        livewire(RenderColorPickerWithSuffixIcon::class)
            ->assertSuccessful();
    });

    it('can render with `suffixIcon()` set via `Closure`', function (): void {
        livewire(RenderColorPickerWithClosureSuffixIcon::class)
            ->assertSuccessful();
    });

    it('can render with `prefixIconColor()`', function (): void {
        livewire(RenderColorPickerWithPrefixIconColor::class)
            ->assertSuccessful();
    });

    it('can render with `suffixIconColor()`', function (): void {
        livewire(RenderColorPickerWithSuffixIconColor::class)
            ->assertSuccessful();
    });

    it('can render with `inlinePrefix()`', function (): void {
        livewire(RenderColorPickerWithInlinePrefix::class)
            ->assertSuccessful();
    });

    it('can render with `prefix()` and `$isInline` parameter', function (): void {
        livewire(RenderColorPickerWithInlinePrefixParam::class)
            ->assertSuccessful()
            ->assertSeeHtml('Color:');
    });

    it('can render with `inlineSuffix()`', function (): void {
        livewire(RenderColorPickerWithInlineSuffix::class)
            ->assertSuccessful();
    });

    it('can render with `suffix()` and `$isInline` parameter', function (): void {
        livewire(RenderColorPickerWithInlineSuffixParam::class)
            ->assertSuccessful()
            ->assertSeeHtml('(hex)');
    });
});

describe('preset colors, eye dropper and copy action rendering', function (): void {
    it('can render `presetColors()` and `eyeDropper()`', function (): void {
        livewire(RenderColorPickerWithPresetColorsAndEyeDropper::class)
            ->assertSuccessful()
            ->assertSeeHtml([
                'format: \'rgb\'',
                'fi-fo-color-picker-panel-has-controls',
                'aria-label="Pick a color from the screen"',
                'role="group" aria-label="Preset colors"',
                'aria-label="Red"',
                'style="background-color: rgb(239, 68, 68)"',
                'aria-label="rgb(17, 24, 39)"',
            ])
            ->assertSeeHtmlInOrder(['</rgb-string-color-picker>', 'fi-fo-color-picker-controls']);
    });

    it('does not render the controls without `presetColors()` or `eyeDropper()`', function (): void {
        livewire(TestComponentWithColorPicker::class)
            ->assertSuccessful()
            ->assertDontSeeHtml(['fi-fo-color-picker-panel-has-controls', 'fi-fo-color-picker-controls']);
    });

    it('escapes the labels and values of `presetColors()`', function (): void {
        livewire(RenderColorPickerWithUnsafePresetColors::class)
            ->assertSuccessful()
            ->assertDontSeeHtml(['<script>alert(1)</script>', '"><script>alert(2)</script>']);
    });

    it('can render `copyable()`', function (): void {
        livewire(RenderCopyableColorPicker::class)
            ->assertSuccessful()
            ->assertSeeHtml('writeText');
    });
});

describe('browser interactions', function (): void {
    it('can render `ColorPicker` in the browser', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/color-picker-browser-test')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/color-picker-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });

    it('selects a preset color', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/color-picker-browser-test');

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="brand-color-picker"]')
            ->assertVisible('[id="form.brandColor-panel"] [aria-label="Preset colors"]')
            ->assertVisible('[aria-label="Pick a color from the screen"]')
            ->assertAttribute('[aria-label="Blue"]', 'aria-pressed', 'false')
            ->assertNoAccessibilityIssues()
            ->click('[aria-label="Blue"]')
            ->assertValue('[data-testid="brand-color-picker"]', '#3b82f6')
            ->assertAttribute('[aria-label="Blue"]', 'aria-pressed', 'true')
            ->assertAttribute('[aria-label="Red"]', 'aria-pressed', 'false');

        $page->inDarkMode()
            ->waitForEvent('networkidle')
            ->click('[data-testid="brand-color-picker"]')
            ->assertVisible('[id="form.brandColor-panel"] [aria-label="Preset colors"]')
            ->assertNoAccessibilityIssues();
    });

    it('stays open when its empty space is clicked', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/color-picker-browser-test');

        $panel = '[id="form.accentColor-panel"]';

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="accent-color-picker"]')
            ->assertVisible($panel)
            ->click("{$panel} .fi-fo-color-picker-controls")
            ->wait(0.5)
            ->assertVisible($panel)
            ->click('[aria-label="Green"]')
            ->assertValue('[data-testid="accent-color-picker"]', '#22c55e');
    });
});

class TestComponentWithColorPicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithRgbColorPicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')->rgb(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithRgbaColorPicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')->rgba(),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithHsl extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')->hsl(),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithHex extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')->rgb()->hex(),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithCustomFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')->format('hsla'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithClosureFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->format(static fn (): string => 'hsl'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->placeholder('Pick a color...'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithClosurePlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->placeholder(static fn (): string => 'Dynamic placeholder'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithNullPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->placeholder('Pick a color...')
                    ->placeholder(null),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithPrefix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->prefix('Color:'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithClosurePrefix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->prefix(static fn (): string => 'Dynamic prefix'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithSuffix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->suffix('(hex)'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithClosureSuffix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->suffix(static fn (): string => 'Dynamic suffix'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithPrefixIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->prefixIcon('heroicon-o-swatch'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithClosurePrefixIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->prefixIcon(static fn (): string => 'heroicon-o-eye-dropper'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithSuffixIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->suffixIcon('heroicon-o-check'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithClosureSuffixIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->suffixIcon(static fn (): string => 'heroicon-o-x-mark'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithPrefixIconColor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->prefixIcon('heroicon-o-swatch')
                    ->prefixIconColor('danger'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithSuffixIconColor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->suffixIcon('heroicon-o-check')
                    ->suffixIconColor('success'),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithInlinePrefix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->prefix('Color:')
                    ->inlinePrefix(),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithInlinePrefixParam extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->prefix('Color:', isInline: true),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithInlineSuffix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->suffix('(hex)')
                    ->inlineSuffix(),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithInlineSuffixParam extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->suffix('(hex)', isInline: true),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithPresetColorsAndEyeDropper extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->rgb()
                    ->presetColors([
                        'rgb(239, 68, 68)' => 'Red',
                        'rgb(17, 24, 39)',
                    ])
                    ->eyeDropper(),
            ])
            ->statePath('data');
    }
}

class RenderColorPickerWithUnsafePresetColors extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->presetColors([
                        '#ef4444' => '<script>alert(1)</script>',
                        '"><script>alert(2)</script>',
                    ]),
            ])
            ->statePath('data');
    }
}

class RenderCopyableColorPicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')
                    ->copyable(),
            ])
            ->statePath('data');
    }
}
