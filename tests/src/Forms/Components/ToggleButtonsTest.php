<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;

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

    it('can set `grouped()`', function (): void {
        $grouped = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->grouped();

        expect($grouped->isGrouped())->toBeTrue();
    });

    it('can set `fullWidth()` and check `isFullWidth()`', function (): void {
        $fullWidth = ToggleButtons::make('status')->options(['a' => 'A'])->fullWidth();
        $notFullWidth = ToggleButtons::make('status')->options(['a' => 'A'])->fullWidth(false);

        expect($fullWidth->isFullWidth())->toBeTrue();
        expect($notFullWidth->isFullWidth())->toBeFalse();
    });

    it('has `isFullWidth()` returning false by default', function (): void {
        $toggleButtons = ToggleButtons::make('status')->options(['a' => 'A']);

        expect($toggleButtons->isFullWidth())->toBeFalse();
    });
});

describe('validation', function (): void {
    it('automatically validates against options array', function (): void {
        livewire(TestComponentWithToggleButtonsValidation::class)
            ->fillForm(['status' => 'active'])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithToggleButtonsValidation::class)
            ->fillForm(['status' => 'archived'])
            ->call('save')
            ->assertHasFormErrors(['status' => ['in']]);
    });

    it('automatically validates multiple options', function (): void {
        livewire(TestComponentWithMultipleToggleButtonsValidation::class)
            ->fillForm(['tags' => ['one', 'two']])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithMultipleToggleButtonsValidation::class)
            ->fillForm(['tags' => ['one', 'four']])
            ->call('save')
            ->assertHasFormErrors(['tags.1' => ['in']]);
    });

    it('passes validation when state is blank', function (): void {
        livewire(TestComponentWithToggleButtonsValidation::class)
            ->fillForm(['status' => null])
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

class TestComponentWithToggleButtonsValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMultipleToggleButtonsValidation extends Livewire
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

    public function save(): void
    {
        $this->form->getState();
    }
}

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

it('can set `fullWidth()` with a `Closure`', function (): void {
    $buttons = ToggleButtons::make('status')
        ->options(['a' => 'A'])
        ->fullWidth(static fn (): bool => true);

    expect($buttons->isFullWidth())->toBeTrue();
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

    it('can render with `fullWidth()`', function (): void {
        livewire(RenderToggleButtonsWithFullWidth::class)->assertSuccessful();
    });

    it('can render with `fullWidth()` set via `Closure`', function (): void {
        livewire(RenderToggleButtonsWithClosureFullWidth::class)->assertSuccessful();
    });

    it('can render with `fullWidth()` and `inline()`', function (): void {
        livewire(RenderToggleButtonsWithFullWidthInline::class)->assertSuccessful();
    });

    it('can render with `fullWidth()` and `grouped()`', function (): void {
        livewire(RenderToggleButtonsWithFullWidthGrouped::class)->assertSuccessful();
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

    it('emits `allowHTML: true` when an option tooltip is `Htmlable`', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->tooltips(['active' => new HtmlString('<strong>Tip</strong>')]),
            ])
            ->fill();

        $html = $field->toHtml();

        expect($html)->toContain('allowHTML: true');
    });

    it('emits `allowHTML: false` when an option tooltip is a plain string', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->tooltips(['active' => 'Plain tooltip']),
            ])
            ->fill();

        $html = $field->toHtml();

        expect($html)->toContain('allowHTML: false');
    });

    it('emits the `fi-width-full` class when `fullWidth()` is set', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->fullWidth(),
            ])
            ->fill();

        expect($field->toHtml())->toContain('fi-width-full');
    });

    it('does not emit the `fi-width-full` class by default', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active']),
            ])
            ->fill();

        expect($field->toHtml())->not->toContain('fi-width-full');
    });

    it('emits the `fi-width-full` class in `grouped()` mode when `fullWidth()` is set', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->grouped()
                    ->fullWidth(),
            ])
            ->fill();

        expect($field->toHtml())->toContain('fi-width-full');
    });
});

it('can render `ToggleButtons` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        $assertLayouts = <<<'JS'
            (() => {
                const stacked = document.querySelector('[data-testid="full-width-stacked-toggle-buttons"]')
                const stackedButtons = [...stacked.querySelectorAll('.fi-btn')]
                const inline = document.querySelector('[data-testid="full-width-inline-toggle-buttons"]')
                const inlineButtonContainers = [...inline.querySelectorAll('.fi-fo-toggle-buttons-btn-ctn')]
                const grouped = document.querySelector('[data-testid="full-width-grouped-toggle-buttons"]')
                const groupedWithLongLabels = document.querySelector('[data-testid="grouped-toggle-buttons-with-long-labels"]')
                const groupedWithLongLabelsButtons = [...groupedWithLongLabels.querySelectorAll('.fi-btn')]

                const stackedWidth = stacked.getBoundingClientRect().width
                const groupedWidth = grouped.getBoundingClientRect().width
                const groupedWithLongLabelsWidth = groupedWithLongLabels.getBoundingClientRect().width
                const inlineBounds = inline.getBoundingClientRect()
                const inlineRows = [...inlineButtonContainers.reduce((rows, container) => {
                    const row = rows.get(container.offsetTop) ?? []
                    row.push(container)
                    rows.set(container.offsetTop, row)

                    return rows
                }, new Map()).values()]
                const inlineRowsFillWidth = inlineRows.every((row) => {
                    const firstButtonBounds = row.at(0).getBoundingClientRect()
                    const lastButtonBounds = row.at(-1).getBoundingClientRect()

                    return Math.abs(firstButtonBounds.left - inlineBounds.left) < 1 &&
                        Math.abs(lastButtonBounds.right - inlineBounds.right) < 1
                })
                const inlineButtonsFillContainers = inlineButtonContainers.every((container) => {
                    return Math.abs(container.querySelector('.fi-btn').getBoundingClientRect().width - container.getBoundingClientRect().width) < 1
                })

                return stackedButtons.every((button) => Math.abs(button.getBoundingClientRect().width - stackedWidth) < 1) &&
                    inlineRows.length > 1 &&
                    inlineRowsFillWidth &&
                    inlineButtonsFillContainers &&
                    Math.abs(groupedWidth - 288) < 1 &&
                    Math.abs(groupedWithLongLabelsWidth - 288) < 1 &&
                    groupedWithLongLabelsButtons.every((button) => button.scrollWidth === button.clientWidth && button.getBoundingClientRect().height > 36)
            })()
            JS;

        visit('/toggle-buttons-test')
            ->assertSee('Test ToggleButtons')
            ->assertNoSmoke()
            ->assertScript($assertLayouts)
            ->assertNoAccessibilityIssues();

        visit('/toggle-buttons-test')
            ->inDarkMode()
            ->assertScript($assertLayouts)
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

class RenderToggleButtonsWithFullWidth extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A', 'b' => 'B'])->fullWidth(),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithClosureFullWidth extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A'])->fullWidth(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithFullWidthInline extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A', 'b' => 'B'])->inline()->fullWidth(),
        ])->statePath('data');
    }
}

class RenderToggleButtonsWithFullWidthGrouped extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            ToggleButtons::make('status')->options(['a' => 'A', 'b' => 'B'])->grouped()->fullWidth(),
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
