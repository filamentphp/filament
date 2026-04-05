<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ToggleButtons;
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
    livewire(TestComponentWithToggleButtons::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithToggleButtons::class)
        ->fillForm(['status' => 'active'])
        ->assertSchemaStateSet(['status' => 'active']);
});

it('can render in boolean mode', function (): void {
    livewire(TestComponentWithBooleanToggleButtons::class)
        ->assertSuccessful();
});

it('can set boolean state', function (): void {
    livewire(TestComponentWithBooleanToggleButtons::class)
        ->fillForm(['is_active' => 1])
        ->assertSchemaStateSet(['is_active' => true]);
});

it('can render in multiple selection mode', function (): void {
    livewire(TestComponentWithMultipleToggleButtons::class)
        ->assertSuccessful();
});

it('can set multiple state', function (): void {
    livewire(TestComponentWithMultipleToggleButtons::class)
        ->fillForm(['tags' => ['one', 'two']])
        ->assertSchemaStateSet(['tags' => ['one', 'two']]);
});

describe('properties', function (): void {
    it('can set `inline()` and check `isInline()`', function (): void {
        $inline = ToggleButtons::make('status')->options(['a' => 'A'])->inline();
        $notInline = ToggleButtons::make('status')->options(['a' => 'A'])->inline(false);

        expect($inline->isInline())->toBeTrue();
        expect($notInline->isInline())->toBeFalse();
    });

    it('has `isInline()` returning false by default', function (): void {
        $toggleButtons = ToggleButtons::make('status')->options(['a' => 'A']);

        expect($toggleButtons->isInline())->toBeFalse();
    });

    it('can set `hiddenButtonLabels()` and check `areButtonLabelsHidden()`', function (): void {
        $hidden = ToggleButtons::make('status')->options(['a' => 'A'])->hiddenButtonLabels();
        $visible = ToggleButtons::make('status')->options(['a' => 'A'])->hiddenButtonLabels(false);

        expect($hidden->areButtonLabelsHidden())->toBeTrue();
        expect($visible->areButtonLabelsHidden())->toBeFalse();
    });

    it('has `areButtonLabelsHidden()` returning false by default', function (): void {
        $toggleButtons = ToggleButtons::make('status')->options(['a' => 'A']);

        expect($toggleButtons->areButtonLabelsHidden())->toBeFalse();
    });

    it('can set `multiple()` and check `isMultiple()`', function (): void {
        $multiple = ToggleButtons::make('tags')->options(['a' => 'A'])->multiple();
        $single = ToggleButtons::make('tags')->options(['a' => 'A'])->multiple(false);

        expect($multiple->isMultiple())->toBeTrue();
        expect($single->isMultiple())->toBeFalse();
    });

    it('has `isMultiple()` returning false by default', function (): void {
        $toggleButtons = ToggleButtons::make('status')->options(['a' => 'A']);

        expect($toggleButtons->isMultiple())->toBeFalse();
    });

    it('has `hasNullableBooleanState()` returning true', function (): void {
        $toggleButtons = ToggleButtons::make('status')->options(['a' => 'A']);

        expect($toggleButtons->hasNullableBooleanState())->toBeTrue();
    });

    it('can set `grouped()` view', function (): void {
        $grouped = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->grouped();

        expect($grouped->getView())->toBe(ToggleButtons::GROUPED_VIEW);
    });
});

class TestComponentWithToggleButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithBooleanToggleButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('is_active')->boolean(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithMultipleToggleButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('tags')
                    ->multiple()
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ]),
            ])
            ->statePath('data');
    }
}

it('can set `inline()` with a `Closure`', function (): void {
    $buttons = ToggleButtons::make('status')
        ->options(['a' => 'A'])
        ->inline(static fn (): bool => true);

    expect($buttons->isInline())->toBeTrue();
});

it('can set `multiple()` with a `Closure`', function (): void {
    $buttons = ToggleButtons::make('tags')
        ->options(['a' => 'A'])
        ->multiple(static fn (): bool => true);

    expect($buttons->isMultiple())->toBeTrue();
});

it('can set `hiddenButtonLabels()` with a `Closure`', function (): void {
    $buttons = ToggleButtons::make('status')
        ->options(['a' => 'A'])
        ->hiddenButtonLabels(static fn (): bool => true);

    expect($buttons->areButtonLabelsHidden())->toBeTrue();
});

it('converts boolean default state to `int`', function (): void {
    $buttons = ToggleButtons::make('active')
        ->boolean()
        ->default(true);

    expect($buttons->getDefaultState())->toBe(1);

    $buttons->default(false);

    expect($buttons->getDefaultState())->toBe(0);
});

it('passes through non-boolean `getDefaultState()` unchanged', function (): void {
    $buttons = ToggleButtons::make('status')
        ->options(['a' => 'A'])
        ->default('a');

    expect($buttons->getDefaultState())->toBe('a');
});

it('returns `isMultiple()` from `hasInValidationOnMultipleValues()`', function (): void {
    $single = ToggleButtons::make('status')->options(['a' => 'A']);
    $multi = ToggleButtons::make('tags')->options(['a' => 'A'])->multiple();

    expect($single->hasInValidationOnMultipleValues())->toBeFalse();
    expect($multi->hasInValidationOnMultipleValues())->toBeTrue();
});

it('can set custom labels for `boolean()`', function (): void {
    $buttons = ToggleButtons::make('active')
        ->boolean(trueLabel: 'Enabled', falseLabel: 'Disabled');

    $options = $buttons->getOptions();

    expect($options[1])->toBe('Enabled');
    expect($options[0])->toBe('Disabled');
});

it('returns fluent `$this` from `boolean()`', function (): void {
    $buttons = ToggleButtons::make('active');

    $result = $buttons->boolean();

    expect($result)->toBe($buttons);
});

it('sets colors and icons via `boolean()`', function (): void {
    $buttons = ToggleButtons::make('active')->boolean();

    $colors = $buttons->getColors();
    $icons = $buttons->getIcons();

    expect($colors[1])->toBe('success');
    expect($colors[0])->toBe('danger');
    expect($icons[1])->not->toBeNull();
    expect($icons[0])->not->toBeNull();
});

it('returns only enabled option keys from `getInValidationRuleValues()`', function (): void {
    $buttons = ToggleButtons::make('status')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
            'archived' => 'Archived',
        ])
        ->disableOptionWhen(static fn (string $value): bool => $value === 'archived');

    $validValues = $buttons->getInValidationRuleValues();

    expect($validValues)->toBe(['active', 'inactive']);
});

describe('rendering', function (): void {
    it('can render with `inline()`', function (): void {
        livewire(RenderToggleButtonsWithInline::class)->assertSuccessful();
    });

    it('can render with `inline()` set via `Closure`', function (): void {
        livewire(RenderToggleButtonsWithClosureInline::class)->assertSuccessful();
    });

    it('can render with `hiddenButtonLabels()`', function (): void {
        livewire(RenderToggleButtonsWithHiddenLabels::class)->assertSuccessful();
    });

    it('can render with `hiddenButtonLabels()` set via `Closure`', function (): void {
        livewire(RenderToggleButtonsWithClosureHiddenLabels::class)->assertSuccessful();
    });

    it('can render with `multiple()` set via `Closure`', function (): void {
        livewire(RenderToggleButtonsWithClosureMultiple::class)->assertSuccessful();
    });

    it('can render with `grouped()` view', function (): void {
        livewire(RenderToggleButtonsWithGrouped::class)->assertSuccessful();
    });

    it('can render with `boolean()` custom labels', function (): void {
        livewire(RenderToggleButtonsWithBooleanCustomLabels::class)
            ->assertSuccessful()
            ->assertSeeHtml('Enabled')
            ->assertSeeHtml('Disabled');
    });

    it('can render with `boolean()` colors and icons', function (): void {
        livewire(RenderToggleButtonsWithBooleanColorsIcons::class)->assertSuccessful();
    });

    it('can render with `disableOptionWhen()`', function (): void {
        livewire(RenderToggleButtonsWithDisabledOption::class)
            ->assertSuccessful()
            ->assertSeeHtml('Active')
            ->assertSeeHtml('Archived');
    });
});

it('can render `ToggleButtons` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/toggle-buttons-test')
            ->assertSee('Test ToggleButtons')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/toggle-buttons-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderToggleButtonsWithInline extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A', 'b' => 'B'])->inline(),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithClosureInline extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A'])->inline(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithHiddenLabels extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A', 'b' => 'B'])->hiddenButtonLabels(),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithClosureHiddenLabels extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A'])->hiddenButtonLabels(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithClosureMultiple extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('tags')->options(['a' => 'A', 'b' => 'B'])->multiple(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithGrouped extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A', 'b' => 'B'])->grouped(),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithBooleanCustomLabels extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('active')->boolean(trueLabel: 'Enabled', falseLabel: 'Disabled'),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithBooleanColorsIcons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('active')->boolean(),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithDisabledOption extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive', 'archived' => 'Archived'])
                ->disableOptionWhen(static fn (string $value): bool => $value === 'archived'),
        ])->statePath('data');
    }
}
