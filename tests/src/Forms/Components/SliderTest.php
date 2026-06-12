<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Slider;
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
    livewire(TestComponentWithSlider::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithSlider::class)
        ->fillForm(['value' => 50])
        ->assertSchemaStateSet(['value' => 50]);
});

it('can render with custom range', function (): void {
    livewire(TestComponentWithCustomRangeSlider::class)
        ->assertSuccessful();
});

it('validates min value', function (): void {
    livewire(TestComponentWithSliderValidation::class)
        ->fillForm(['value' => -10])
        ->call('save')
        ->assertHasFormErrors(['value']);
});

it('validates max value', function (): void {
    livewire(TestComponentWithSliderValidation::class)
        ->fillForm(['value' => 200])
        ->call('save')
        ->assertHasFormErrors(['value']);
});

describe('min/max values', function (): void {
    it('defaults `getMinValue()` to `0`', function (): void {
        $slider = Slider::make('value');

        expect($slider->getMinValue())->toBe(0);
    });

    it('defaults `getMaxValue()` to `100`', function (): void {
        $slider = Slider::make('value');

        expect($slider->getMaxValue())->toBe(100);
    });

    it('can set `minValue()` and `maxValue()`', function (): void {
        $slider = Slider::make('value')
            ->minValue(10)
            ->maxValue(50);

        expect($slider->getMinValue())->toBe(10);
        expect($slider->getMaxValue())->toBe(50);
    });

    it('can set `minValue()` with a `Closure`', function (): void {
        $slider = Slider::make('value')
            ->minValue(static fn (): int => 5);

        expect($slider->getMinValue())->toBe(5);
    });

    it('can set `maxValue()` with a `Closure`', function (): void {
        $slider = Slider::make('value')
            ->maxValue(static fn (): int => 200);

        expect($slider->getMaxValue())->toBe(200);
    });

    it('can use `range()` shortcut to set both', function (): void {
        $slider = Slider::make('value')
            ->range(10, 90);

        expect($slider->getMinValue())->toBe(10);
        expect($slider->getMaxValue())->toBe(90);
    });
});

describe('padding logic', function (): void {
    it('returns min value unchanged when no padding', function (): void {
        $slider = Slider::make('value')
            ->minValue(10)
            ->maxValue(100);

        expect($slider->getMinValueWithPadding())->toBe(10);
        expect($slider->getMaxValueWithPadding())->toBe(100);
    });

    it('applies numeric padding to both min and max', function (): void {
        $slider = Slider::make('value')
            ->minValue(0)
            ->maxValue(100)
            ->rangePadding(10);

        expect($slider->getMinValueWithPadding())->toBe(10);
        expect($slider->getMaxValueWithPadding())->toBe(90);
    });

    it('applies array padding separately to min and max', function (): void {
        $slider = Slider::make('value')
            ->minValue(0)
            ->maxValue(100)
            ->rangePadding([5, 15]);

        expect($slider->getMinValueWithPadding())->toBe(5);
        expect($slider->getMaxValueWithPadding())->toBe(85);
    });
});

describe('behavior', function (): void {
    it('defaults `getBehavior()` to `Tap`', function (): void {
        $slider = Slider::make('value');

        expect($slider->getBehavior())->toBe(Slider\Enums\Behavior::Tap);
    });

    it('returns `none` from `getBehaviorForJs()` when behavior is `null`', function (): void {
        $slider = Slider::make('value')
            ->behavior(null);

        expect($slider->getBehaviorForJs())->toBe('none');
    });

    it('returns hyphenated string from `getBehaviorForJs()` with array of behaviors', function (): void {
        $slider = Slider::make('value')
            ->behavior([Slider\Enums\Behavior::Tap, Slider\Enums\Behavior::Drag]);

        expect($slider->getBehaviorForJs())->toBe('tap-drag');
    });
});

describe('tooltips', function (): void {
    it('defaults `hasTooltips()` to `false`', function (): void {
        $slider = Slider::make('value');

        expect($slider->hasTooltips())->toBeFalse();
    });

    it('can enable `tooltips()`', function (): void {
        $slider = Slider::make('value')
            ->tooltips();

        expect($slider->hasTooltips())->toBeTrue();
    });

    it('returns `false` for `hasTooltips()` when array is all `false`', function (): void {
        $slider = Slider::make('value')
            ->tooltips([false, false]);

        expect($slider->hasTooltips())->toBeFalse();
    });

    it('returns `true` for `hasTooltips()` when array has any non-`false` value', function (): void {
        $slider = Slider::make('value')
            ->tooltips([true, false]);

        expect($slider->hasTooltips())->toBeTrue();
    });
});

describe('orientation', function (): void {
    it('defaults `isVertical()` to `false`', function (): void {
        $slider = Slider::make('value');

        expect($slider->isVertical())->toBeFalse();
    });

    it('can set `vertical()`', function (): void {
        $slider = Slider::make('value')->vertical();

        expect($slider->isVertical())->toBeTrue();
    });

    it('can set `vertical()` with a `Closure`', function (): void {
        $slider = Slider::make('value')
            ->vertical(static fn (): bool => true);

        expect($slider->isVertical())->toBeTrue();
    });
});

describe('pips', function (): void {
    it('returns `null` for `getPipsMode()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getPipsMode())->toBeNull();
    });

    it('can set `pips()` with mode and density', function (): void {
        $slider = Slider::make('value')
            ->pips(Slider\Enums\PipsMode::Range, density: 5);

        expect($slider->getPipsMode())->toBe(Slider\Enums\PipsMode::Range);
        expect($slider->getPipsDensity())->toBe(5);
    });

    it('defaults `arePipsStepped()` to `false`', function (): void {
        $slider = Slider::make('value');

        expect($slider->arePipsStepped())->toBeFalse();
    });

    it('can set `steppedPips()`', function (): void {
        $slider = Slider::make('value')->steppedPips();

        expect($slider->arePipsStepped())->toBeTrue();
    });
});

describe('decimal places', function (): void {
    it('returns `null` for `getDecimalPlaces()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getDecimalPlaces())->toBeNull();
    });

    it('can set `decimalPlaces()`', function (): void {
        $slider = Slider::make('value')
            ->decimalPlaces(2);

        expect($slider->getDecimalPlaces())->toBe(2);
    });

    it('can set `decimalPlaces()` with a `Closure`', function (): void {
        $slider = Slider::make('value')
            ->decimalPlaces(static fn (): int => 3);

        expect($slider->getDecimalPlaces())->toBe(3);
    });
});

describe('differences', function (): void {
    it('returns `null` for `getMinDifference()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getMinDifference())->toBeNull();
    });

    it('can set `minDifference()`', function (): void {
        $slider = Slider::make('value')
            ->minDifference(10);

        expect($slider->getMinDifference())->toBe(10);
    });

    it('returns `null` for `getMaxDifference()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getMaxDifference())->toBeNull();
    });

    it('can set `maxDifference()`', function (): void {
        $slider = Slider::make('value')
            ->maxDifference(50);

        expect($slider->getMaxDifference())->toBe(50);
    });
});

describe('default state from `setUp()` closure', function (): void {
    it('defaults state to `minValue` (0)', function (): void {
        $slider = Slider::make('value');

        expect($slider->getDefaultState())->toBe(0);
    });

    it('defaults state to custom `minValue`', function (): void {
        $slider = Slider::make('value')
            ->minValue(25);

        expect($slider->getDefaultState())->toBe(25);
    });
});

describe('step validation closures', function (): void {
    it('validates `step()` of `1` as `integer`', function (): void {
        livewire(TestComponentWithStepSliderInteger::class)
            ->fillForm(['value' => 5])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects non-integer when `step()` is `1`', function (): void {
        livewire(TestComponentWithStepSliderInteger::class)
            ->fillForm(['value' => 5.5])
            ->call('save')
            ->assertHasFormErrors(['value']);
    });

    it('validates `step()` of `0.5` as `multiple_of:0.5`', function (): void {
        livewire(TestComponentWithStepSliderDecimal::class)
            ->fillForm(['value' => 2.5])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects non-multiple when `step()` is `0.5`', function (): void {
        livewire(TestComponentWithStepSliderDecimal::class)
            ->fillForm(['value' => 2.3])
            ->call('save')
            ->assertHasFormErrors(['value']);
    });
});

describe('padded validation closures', function (): void {
    it('rejects values below min with padding', function (): void {
        livewire(TestComponentWithPaddedSlider::class)
            ->fillForm(['value' => 5])
            ->call('save')
            ->assertHasFormErrors(['value']);
    });

    it('accepts values within padded range', function (): void {
        livewire(TestComponentWithPaddedSlider::class)
            ->fillForm(['value' => 15])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects values above max with padding', function (): void {
        livewire(TestComponentWithPaddedSlider::class)
            ->fillForm(['value' => 95])
            ->call('save')
            ->assertHasFormErrors(['value']);
    });
});

describe('fill track', function (): void {
    it('returns `null` for `getFillTrack()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getFillTrack())->toBeNull();
    });

    it('can set `fillTrack()` with default value', function (): void {
        $slider = Slider::make('value')->fillTrack();

        expect($slider->getFillTrack())->toBe([true, false]);
    });

    it('can set `fillTrack()` with custom array', function (): void {
        $slider = Slider::make('value')
            ->fillTrack([false, true]);

        expect($slider->getFillTrack())->toBe([false, true]);
    });

    it('can clear `fillTrack()` with `null`', function (): void {
        $slider = Slider::make('value')
            ->fillTrack()
            ->fillTrack(null);

        expect($slider->getFillTrack())->toBeNull();
    });
});

describe('RTL', function (): void {
    it('can set `rtl()`', function (): void {
        $slider = Slider::make('value')->rtl();

        expect($slider->isRtl())->toBeTrue();
    });

    it('returns `true` for `isRtl()` when vertical', function (): void {
        $slider = Slider::make('value')->vertical();

        expect($slider->isRtl())->toBeTrue();
    });

    it('can set `rtl()` to `false` explicitly', function (): void {
        $slider = Slider::make('value')
            ->rtl(false);

        expect($slider->isRtl())->toBeFalse();
    });
});

describe('non-linear points', function (): void {
    it('returns `null` for `getNonLinearPoints()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getNonLinearPoints())->toBeNull();
    });

    it('can set `nonLinearPoints()`', function (): void {
        $points = ['0%' => 0, '50%' => [25, 75], '100%' => 100];
        $slider = Slider::make('value')
            ->nonLinearPoints($points);

        expect($slider->getNonLinearPoints())->toBe($points);
    });

    it('can clear `nonLinearPoints()` with `null`', function (): void {
        $slider = Slider::make('value')
            ->nonLinearPoints(['0%' => 0, '100%' => 100])
            ->nonLinearPoints(null);

        expect($slider->getNonLinearPoints())->toBeNull();
    });
});

describe('pips values and filter', function (): void {
    it('returns `null` for `getPipsValues()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getPipsValues())->toBeNull();
    });

    it('can set `pipsValues()` with an array', function (): void {
        $slider = Slider::make('value')
            ->pipsValues([0, 25, 50, 75, 100]);

        expect($slider->getPipsValues())->toBe([0, 25, 50, 75, 100]);
    });

    it('returns `null` for `getPipsFilter()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getPipsFilter())->toBeNull();
    });

    it('returns `null` for `getPipsFormatter()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getPipsFormatter())->toBeNull();
    });
});

describe('step', function (): void {
    it('returns `null` for `getStep()` by default', function (): void {
        $slider = Slider::make('value');

        expect($slider->getStep())->toBeNull();
    });

    it('can set `step()`', function (): void {
        $slider = Slider::make('value')
            ->step(5);

        expect($slider->getStep())->toBe(5);
    });

    it('can set `step()` with a `Closure`', function (): void {
        $slider = Slider::make('value')
            ->step(static fn (): float => 0.1);

        expect($slider->getStep())->toBe(0.1);
    });
});

describe('rendering', function (): void {
    it('can render with `minValue()` set via `Closure`', function (): void {
        livewire(RenderSliderWithClosureMinValue::class)->assertSuccessful();
    });

    it('can render with `maxValue()` set via `Closure`', function (): void {
        livewire(RenderSliderWithClosureMaxValue::class)->assertSuccessful();
    });

    it('can render with `behavior(null)`', function (): void {
        livewire(RenderSliderWithNullBehavior::class)->assertSuccessful();
    });

    it('can render with `behavior()` array', function (): void {
        livewire(RenderSliderWithArrayBehavior::class)->assertSuccessful();
    });

    it('can render with `tooltips()`', function (): void {
        livewire(RenderSliderWithTooltips::class)->assertSuccessful();
    });

    it('can render with `tooltips()` array', function (): void {
        livewire(RenderSliderWithTooltipsArray::class)->assertSuccessful();
    });

    it('can render with `vertical()`', function (): void {
        livewire(RenderSliderWithVertical::class)->assertSuccessful();
    });

    it('can render with `vertical()` set via `Closure`', function (): void {
        livewire(RenderSliderWithClosureVertical::class)->assertSuccessful();
    });

    it('can render with `pips()`', function (): void {
        livewire(RenderSliderWithPips::class)->assertSuccessful();
    });

    it('can render with `steppedPips()`', function (): void {
        livewire(RenderSliderWithSteppedPips::class)->assertSuccessful();
    });

    it('can render with `decimalPlaces()`', function (): void {
        livewire(RenderSliderWithDecimalPlaces::class)->assertSuccessful();
    });

    it('can render with `decimalPlaces()` set via `Closure`', function (): void {
        livewire(RenderSliderWithClosureDecimalPlaces::class)->assertSuccessful();
    });

    it('can render with `minDifference()`', function (): void {
        livewire(RenderSliderWithMinDifference::class)->assertSuccessful();
    });

    it('can render with `maxDifference()`', function (): void {
        livewire(RenderSliderWithMaxDifference::class)->assertSuccessful();
    });

    it('can render with `fillTrack()`', function (): void {
        livewire(RenderSliderWithFillTrack::class)->assertSuccessful();
    });

    it('can render with `fillTrack()` custom array', function (): void {
        livewire(RenderSliderWithFillTrackCustom::class)->assertSuccessful();
    });

    it('can render with `fillTrack(null)`', function (): void {
        livewire(RenderSliderWithFillTrackNull::class)->assertSuccessful();
    });

    it('can render with `rtl()`', function (): void {
        livewire(RenderSliderWithRtl::class)->assertSuccessful();
    });

    it('can render with `rtl(false)`', function (): void {
        livewire(RenderSliderWithRtlFalse::class)->assertSuccessful();
    });

    it('can render with `nonLinearPoints()`', function (): void {
        livewire(RenderSliderWithNonLinearPoints::class)->assertSuccessful();
    });

    it('can render with `nonLinearPoints(null)`', function (): void {
        livewire(RenderSliderWithNonLinearPointsNull::class)->assertSuccessful();
    });

    it('can render with `pipsValues()`', function (): void {
        livewire(RenderSliderWithPipsValues::class)->assertSuccessful();
    });

    it('can render with `step()`', function (): void {
        livewire(RenderSliderWithStep::class)->assertSuccessful();
    });

    it('can render with `step()` set via `Closure`', function (): void {
        livewire(RenderSliderWithClosureStep::class)->assertSuccessful();
    });

    it('can render with `rangePadding()` numeric', function (): void {
        livewire(RenderSliderWithRangePadding::class)->assertSuccessful();
    });

    it('can render with `rangePadding()` array', function (): void {
        livewire(RenderSliderWithRangePaddingArray::class)->assertSuccessful();
    });
});

it('can render `Slider` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/slider-browser-test')
            ->assertSee('Test Slider')
            ->assertNoSmoke();

        visit('/slider-browser-test')
            ->inDarkMode()
            ->assertNoSmoke();
    });
});

class TestComponentWithStepSliderInteger extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->minValue(0)
                    ->maxValue(10)
                    ->step(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithStepSliderDecimal extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->minValue(0)
                    ->maxValue(10)
                    ->step(0.5),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithPaddedSlider extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->minValue(0)
                    ->maxValue(100)
                    ->rangePadding(10),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithSlider extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCustomRangeSlider extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->minValue(10)
                    ->maxValue(50),
            ])
            ->statePath('data');
    }
}

class TestComponentWithSliderValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->minValue(0)
                    ->maxValue(100),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class RenderSliderWithClosureMinValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->minValue(static fn (): int => 5)])->statePath('data');
    }
}

class RenderSliderWithClosureMaxValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->maxValue(static fn (): int => 200)])->statePath('data');
    }
}

class RenderSliderWithNullBehavior extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->behavior(null)])->statePath('data');
    }
}

class RenderSliderWithArrayBehavior extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->behavior([Slider\Enums\Behavior::Tap, Slider\Enums\Behavior::Drag])])->statePath('data');
    }
}

class RenderSliderWithTooltips extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->tooltips()])->statePath('data');
    }
}

class RenderSliderWithTooltipsArray extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->tooltips([true, false])])->statePath('data');
    }
}

class RenderSliderWithVertical extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->vertical()])->statePath('data');
    }
}

class RenderSliderWithClosureVertical extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->vertical(static fn (): bool => true)])->statePath('data');
    }
}

class RenderSliderWithPips extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->pips(Slider\Enums\PipsMode::Range, density: 5)])->statePath('data');
    }
}

class RenderSliderWithSteppedPips extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->steppedPips()])->statePath('data');
    }
}

class RenderSliderWithDecimalPlaces extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->decimalPlaces(2)])->statePath('data');
    }
}

class RenderSliderWithClosureDecimalPlaces extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->decimalPlaces(static fn (): int => 3)])->statePath('data');
    }
}

class RenderSliderWithMinDifference extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->minDifference(10)])->statePath('data');
    }
}

class RenderSliderWithMaxDifference extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->maxDifference(50)])->statePath('data');
    }
}

class RenderSliderWithFillTrack extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->fillTrack()])->statePath('data');
    }
}

class RenderSliderWithFillTrackCustom extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->fillTrack([false, true])])->statePath('data');
    }
}

class RenderSliderWithFillTrackNull extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->fillTrack()->fillTrack(null)])->statePath('data');
    }
}

class RenderSliderWithRtl extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->rtl()])->statePath('data');
    }
}

class RenderSliderWithRtlFalse extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->rtl(false)])->statePath('data');
    }
}

class RenderSliderWithNonLinearPoints extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->nonLinearPoints(['0%' => 0, '50%' => [25, 75], '100%' => 100])])->statePath('data');
    }
}

class RenderSliderWithNonLinearPointsNull extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->nonLinearPoints(['0%' => 0, '100%' => 100])->nonLinearPoints(null)])->statePath('data');
    }
}

class RenderSliderWithPipsValues extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->pipsValues([0, 25, 50, 75, 100])])->statePath('data');
    }
}

class RenderSliderWithStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->step(5)])->statePath('data');
    }
}

class RenderSliderWithClosureStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->step(static fn (): float => 0.1)])->statePath('data');
    }
}

class RenderSliderWithRangePadding extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->rangePadding(10)])->statePath('data');
    }
}

class RenderSliderWithRangePaddingArray extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Slider::make('value')->rangePadding([5, 15])])->statePath('data');
    }
}
