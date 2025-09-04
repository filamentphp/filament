---
title: Slider
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The slider component allows you to drag a handle across a track to select one or more numeric values:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
```

<AutoScreenshot name="forms/fields/slider/simple" alt="Slider" version="4.x" />

The [noUiSlider](https://refreshless.com/nouislider) package is used for this component, and much of its API is based upon that library.

<Aside variant="warning">
    Due to their nature, slider fields can never be empty. The value of the field can never be `null` or an empty array. If a slider field is empty, the user will not have a handle to drag across the track.

    Because of this, slider fields have a default value set out of the box, which is set to the minimum value allowed in the [range](#controlling-the-range-of-the-slider) of the slider. The default value is used when a form is empty, for example on the Create page of a resource. To learn more about default values, check out the [`default()` documentation](overview#setting-the-default-value-of-a-field).
</Aside>

## Controlling the range of the slider

The minimum and maximum values that can be selected by the slider are 0 and 100 by default. Filament will automatically apply validation rules to ensure that users cannot exceed these values. You can adjust these with the `range()` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 40, maxValue: 80)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `range()` method also accepts functions to dynamically calculate them. You can inject various utilities into the functions as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/slider/range" alt="Slider with a customized range" version="4.x" />

### Controlling the step size

By default, users can select any decimal value between the minimum and maximum. You can restrict the values to a specific step size using the `step()` method. Filament will automatically apply validation rules to ensure that users cannot deviate from this step size:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->step(10)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `step()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Limiting the number of decimal places

If you would rather allow the user to select any decimal value between the minimum and maximum instead of restricting them to a certain step size, you can define a number of decimal places that their selected values will be rounded to using the `decimalPlaces()` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->decimalPlaces(2)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `decimalPlaces()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Controlling the behavioral padding of the track

By default, users can select any value across the entire length of the track. You can add behavioral padding to the track using the `rangePadding()` method. This will ensure that the selected value is always at least a certain distance from the edges of the track. The minimum and maximum value validation that Filament applies to the slider by default will take the padding into account to ensure that users cannot exceed the padded range:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->rangePadding(10)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `rangePadding()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

In this example, even though the minimum value is 0 and the maximum value is 100, the user will only be able to select values between 10 and 90. The padding will be applied to both ends of the track, so the selected value will always be at least 10 units away from the edges of the track.

If you would like to control the padding on each side of the track separately, you can pass an array of two values to the `rangePadding()` method. The first value will be applied to the start of the track, and the second value will be applied to the end of the track:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->rangePadding([10, 20])
```

### Right-to-left tracks

By default, a track operates left-to-right. If the user is using a right-to-left locale (e.g. Arabic), the track will be displayed right-to-left automatically. You can also force the track to be displayed right-to-left using the `rtl()` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->rtl()
```

Optionally, you may pass a boolean value to control if the slider should be right-to-left or not:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->rtl(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `rtl()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Adding multiple values to a slider

If the slider is set to an array of values, the user will be able to drag multiple handles across the track within the allowed range. Make sure that the slider has a [`default()` value](overview#setting-the-default-value-of-a-field) set to an array of values to use when a form is empty:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->default([20, 70])
```

<AutoScreenshot name="forms/fields/slider/multiple" alt="Slider with multiple values" version="4.x" />

If you're saving multiple slider values using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'slider' => 'array',
        ];
    }

    // ...
}
```

## Using a vertical track

You can display the slider as a vertical track instead of horizontal, you can use the `vertical()` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->vertical()
```

<AutoScreenshot name="forms/fields/slider/vertical" alt="Vertical slider" version="4.x" />

Optionally, you may pass a boolean value to control if the slider should be vertical or not:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->vertical(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `vertical()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Top-to-bottom tracks

By default, a vertical track operates bottom-to-top. In [noUiSlider](https://refreshless.com/nouislider), this is the [right-to-left behavior](#right-to-left-tracks). To assign the minimum value to the top of the track, you can use the `rtl(false)` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->vertical()
    ->rtl(false)
```

## Adding tooltips to handles

You can add tooltips to the handles of the slider using the `tooltips()` method. The tooltip will display the current value of the handle:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->tooltips()
```

<AutoScreenshot name="forms/fields/slider/tooltips" alt="Slider with tooltips" version="4.x" />

Optionally, you may pass a boolean value to control if the slider should have tooltips or not:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->tooltips(FeatureFlag::active())
```

When using multiple handles, multiple tooltips will be displayed, one for each handle. Tooltips also work with [vertical tracks](#using-a-vertical-track).

<AutoScreenshot name="forms/fields/slider/tooltips-vertical" alt="Vertical slider with tooltips" version="4.x" />

### Custom tooltip formatting

You can use JavaScript to customize the formatting of a tooltip. Pass a `RawJs` object to the `tooltips()` method, containing a JavaScript string expression to use. The current value to format will be available in the `$value` variable:

```php
use Filament\Forms\Components\Slider;
use Filament\Support\RawJs;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->tooltips(RawJs::make(<<<'JS'
        `$${$value.toFixed(2)}`
        JS))
```

<AutoScreenshot name="forms/fields/slider/tooltips-formatting" alt="Slider with custom tooltip formatting" version="4.x" />

### Controlling tooltips for multiple handles individually

If the slider is set to an array of values, you can control the tooltips for each handle separately by passing an array of values to the `tooltips()` method. The first value will be applied to the first handle, and the second value will be applied to the second handle, and so on:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->tooltips([true, false])
```

<AutoScreenshot name="forms/fields/slider/tooltips-multiple" alt="Slider with multiple tooltips" version="4.x" />

## Filling a track with color

By default, the color of the track is not affected by the position of any handles it has. When using an individual handle, you can fill the part of the track before the handle with color using the `fillTrack()` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->fillTrack()
```

<AutoScreenshot name="forms/fields/slider/fill" alt="Slider with fill" version="4.x" />

Optionally, you may pass a boolean value to control if the slider should be filled or not:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->fillTrack(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `fillTrack()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

When using multiple handles, you must manually specify which parts of the track should be filled by passing an array of `true` and `false` values, one for each section. The total number of values should be one more than the number of handles. The first value will be applied to the section before the first handle, the second value will be applied to the section between the first and second handles, and so on:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->fillTrack([false, true, false])
```

<AutoScreenshot name="forms/fields/slider/fill-multiple" alt="Slider with multiple fills" version="4.x" />

Filling also works on [vertical tracks](#using-a-vertical-track):

<AutoScreenshot name="forms/fields/slider/fill-vertical" alt="Vertical slider with fill" version="4.x" />

## Adding pips to tracks

You can add pips to tracks, which are small markers that indicate the position of the handles. You can add pips to the track using the `pips()` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips()
```

<AutoScreenshot name="forms/fields/slider/pips" alt="Slider with pips" version="4.x" />

Pips also work when there are multiple handles:

<AutoScreenshot name="forms/fields/slider/pips-multiple" alt="Slider with multiple pips" version="4.x" />

You can also add pips to [vertical tracks](#using-a-vertical-track):

<AutoScreenshot name="forms/fields/slider/pips-vertical" alt="Vertical slider with pips" version="4.x" />

### Adjusting the density of pips

By default, pips are displayed at a density of `10`. This means that for every 10 units of the track, a pip will be displayed. You can adjust this density using the `density` parameter of the `pips()` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips(density: 5)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `density` parameter also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/slider/pips-density" alt="Slider with a custom pips density" version="4.x" />

### Custom pip label formatting

You can use JavaScript to customize the formatting of a pip label. Pass a `RawJs` object to the `pipsFormatter()` method, containing a JavaScript string expression to use. The current value to format will be available in the `$value` variable:

```php
use Filament\Forms\Components\Slider;
use Filament\Support\RawJs;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips()
    ->pipsFormatter(RawJs::make(<<<'JS'
        `$${$value.toFixed(2)}`
        JS))
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `pipsFormatter()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/slider/pips-formatting" alt="Slider with custom pips formatting" version="4.x" />

### Adding pip labels to steps of the track

If you are using [steps](#controlling-the-step-size) to restrict the movement of the track, you can add a pip label to each step. To do this, pass a `PipsMode::Steps` object to the `pips()` method:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->step(10)
    ->pips(PipsMode::Steps)
```

<AutoScreenshot name="forms/fields/slider/pips-steps" alt="Slider with pips on steps" version="4.x" />

If you would like to add additional pips to the track without labels, you can [adjust the density](#adjusting-the-density-of-pips) of the pips as well:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->step(10)
    ->pips(PipsMode::Steps, density: 5)
```

<AutoScreenshot name="forms/fields/slider/pips-steps-density" alt="Slider with pips on steps and a custom density" version="4.x" />

### Adding pip labels to percentage positions of the track

If you would like to add pip labels to the track at specific percentage positions, you can pass a `PipsMode::Positions` object to the `pips()` method. The percentage positions need to be defined in the `pipsValues()` method in an array of numbers:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips(PipsMode::Positions)
    ->pipsValues([0, 25, 50, 75, 100])
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `pipsValues()` method also accepts a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/slider/pips-positions" alt="Slider with pips on positions" version="4.x" />

The [density](#adjusting-the-density-of-pips) still controls the spacing of the pips without labels.

### Adding a set number of pip labels to the track

If you would like to add a set number of pip labels to the track, you can pass a `PipsMode::Count` object to the `pips()` method. The number of pips need to be defined in the `pipsValues()` method:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips(PipsMode::Count)
    ->pipsValues(5)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `pipsValues()` method also accepts a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/slider/pips-count" alt="Slider with a set number of pips" version="4.x" />

The [density](#adjusting-the-density-of-pips) still controls the spacing of the pips without labels.

### Adding pip labels to set values on the track

Instead of defining [percentage positions](#adding-pip-labels-to-percentage-positions-of-the-track) or a [set number](#adding-a-set-number-of-pip-labels-to-the-track) of pip labels, you can also define a set of values to use for the pip labels. To do this, pass a `PipsMode::Values` object to the `pips()` method. The values need to be defined in the `pipsValues()` method in an array of numbers:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips(PipsMode::Values)
    ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `pipsValues()` method also accepts a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/slider/pips-values" alt="Slider with pips on values" version="4.x" />

The [density](#adjusting-the-density-of-pips) still controls the spacing of the pips without labels:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips(PipsMode::Values, density: 5)
    ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
```

<AutoScreenshot name="forms/fields/slider/pips-values-density" alt="Slider with pips on values and a custom density" version="4.x" />

### Manually filtering pips

For even more control over how pips are displayed on a track, you can use a JavaScript expression. The JavaScript expression should return different numbers based on the pip's appearance:

- The expression should return `1` if a large pip label should be displayed.
- The expression should return `2` if a small pip label should be displayed.
- The expression should return `0` if a pip should be displayed without a label.
- The expression should return `-1` if a pip should not be displayed at all.

The [density](#adjusting-the-density-of-pips) of the pips will control which values get passed to the JavaScript expression. The expression should use the `$value` variable to access the current value of the pip. The expression should be defined in a `RawJs` object and passed to the `pipsFilter()` method:

```php
use Filament\Forms\Components\Slider;
use Filament\Support\RawJs;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->pips(density: 5)
    ->pipsFilter(RawJs::make(<<<'JS'
        ($value % 50) === 0
            ? 1
            : ($value % 10) === 0
                ? 2
                : ($value % 25) === 0
                    ? 0
                    : -1
        JS))
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `pipsFilter()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

In this example the `%` operator is used to determine the divisibility of the pip value. The expression will return `1` for every 50 units, `2` for every 10 units, `0` for every 25 units, and `-1` for all other values:

<AutoScreenshot name="forms/fields/slider/pips-filter" alt="Slider with pips filter" version="4.x" />

## Setting a minimum difference between handles

To set a minimum distance between handles, you can use the `minDifference()` method, passing a number to it. This represents the real difference between the values of both handles, not their visual distance:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->minDifference(10)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `minDifference()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<Aside variant="warning">
    The `minDifference()` method does not impact the validation of the slider. A skilled user could manipulate the value of the slider using JavaScript to select a value that does not align with the minimum difference. They will still be prevented from selecting a value outside the range of the slider.
</Aside>

## Setting a maximum difference between handles

To set a maximum distance between handles, you can use the `maxDifference()` method, passing a number to it. This represents the real difference between the values of both handles, not their visual distance:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->maxDifference(40)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `maxDifference()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<Aside variant="warning">
    The `maxDifference()` method does not impact the validation of the slider. A skilled user could manipulate the value of the slider using JavaScript to select a value that does not align with the maximum difference. They will still be prevented from selecting a value outside the range of the slider.
</Aside>

## Controlling the general behavior of the slider

The `behavior()` method of the slider allows you to pass one or more `Behavior` objects to control the behavior of the slider. The available options are:

- `Behavior::Tap` - The slider will smoothly move to the position of the tap when the user clicks on the track. This is a default behavior, so when applying another behavior, you should also include this one in the array if you want to preserve it.
- `Behavior::Drag` - When there are two handles on the track, the user can drag the track to move both handles at the same time. The [`fillTrack([false, true, false])`](#filling-a-track-with-color) method must be used to ensure that the user has something to drag.
- `Behavior::Drag` and `Behavior::Fixed` - When there are two handles on the track, the user can drag the track to move both handles at the same time, but they cannot change the distance between them. The [`fillTrack([false, true, false])`](#filling-a-track-with-color) method must be used to ensure that the user has something to drag. Be aware that the distance between the handles is not automatically validated on the backend, so a skilled user could manipulate the value of the slider using JavaScript to select a value with a different distance. They will still be prevented from selecting a value outside the range of the slider.
- `Behavior::Unconstrained` - When there are multiple handles on the track, they can be dragged past each other. The [`minDifference()`](#setting-a-minimum-difference-between-handles) and [`maxDifference()`](#setting-a-maximum-difference-between-handles) methods will not work with this behavior.
- `Behavior::SmoothSteps` - While dragging handles, they will not snap to the [steps](#controlling-the-step-size) of the track. Once the user releases the handle, it will snap to the nearest step.

For example, to use `Behavior::Tap`, `Behavior::Drag` and `Behavior::SmoothSteps` all at once:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\Behavior;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->behavior([Behavior::Tap, Behavior::Drag, Behavior::SmoothSteps])
```

To disable all behavior, including the default `Behavior::Tap`, you can use the `behavior(null)` method:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->behavior(null)
```

## Non-linear tracks

You can create non-linear tracks, where certain parts of the track are compressed or expanded, by defining an array of percentage points in the `nonLinearPoints()` method of the slider. Each percentage key of the array should have a corresponding real value, which will be used to calculate the position of the handle on the track. The example below features [pips](#adding-pips-to-tracks) to demonstrate the non-linear behavior of the track:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->nonLinearPoints(['20%' => 50, '50%' => 75])
    ->pips()
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `nonLinearPoints()` method also accepts a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

When using a non-linear track, you can also control the stepping for each section. By defining an array of two numbers for each percentage point, the first number will be used as the real value for percentage position, and the second number will be used as the step size for that section, active until the next percentage point:

```php
use Filament\Forms\Components\Slider;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 100)
    ->nonLinearPoints(['20%' => [50, 5], '50%' => [75, 1]])
    ->pips()
```

<Aside variant="warning">
    When using a non-linear track, the step values of certain track sections do not affect the validation of the slider. A skilled user could manipulate the value of the slider using JavaScript to select a value that does not align with a step value in the track. They will still be prevented from selecting a value outside the range of the slider.
</Aside>

When using [pips](#adding-pips-to-tracks) with a non-linear track, you can ensure that pip labels are rounded and only displayed at selectable positions on the track. Otherwise, the stepping of a non-linear portion of the track could add labels to positions that are not selectable. To do this, use the `steppedPips()` method:

```php
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;

Slider::make('slider')
    ->range(minValue: 0, maxValue: 10000)
    ->nonLinearPoints(['10%' => [500, 500], '50%' => [4000, 1000]])
    ->pips(PipsMode::Positions, density: 4)
    ->pipsValues([0, 25, 50, 75, 100])
    ->steppedPips()
```
