<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render', function (): void {
    livewire(TestComponentWithColorPicker::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithColorPicker::class)
        ->fillForm(['color' => '#ff0000'])
        ->assertSchemaStateSet(['color' => '#ff0000']);
});

it('can render with RGB format', function (): void {
    livewire(TestComponentWithRgbColorPicker::class)
        ->assertSuccessful();
});

it('can render with RGBA format', function (): void {
    livewire(TestComponentWithRgbaColorPicker::class)
        ->assertSuccessful();
});

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

it('can set custom `format()`', function (): void {
    $picker = ColorPicker::make('color')
        ->format('hsla');

    expect($picker->getFormat())->toBe('hsla');
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

it('can set `format()` with a `Closure`', function (): void {
    $picker = ColorPicker::make('color')
        ->format(static fn (): string => 'hsl');

    expect($picker->getFormat())->toBe('hsl');
});

it('returns fluent `$this` from format helpers', function (): void {
    $picker = ColorPicker::make('color');

    expect($picker->hex())->toBe($picker);
    expect($picker->hsl())->toBe($picker);
    expect($picker->rgb())->toBe($picker);
    expect($picker->rgba())->toBe($picker);
    expect($picker->format('hex'))->toBe($picker);
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

describe('rendering', function (): void {
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

it('can render `ColorPicker` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/color-picker-test')
            ->assertSee('Test Color')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/color-picker-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

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
