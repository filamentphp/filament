<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
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

it('can render a single `ToggleButtons` field after a malformed Livewire state update', function (string $componentClass): void {
    livewire($componentClass)
        ->set('data.status', ['active'])
        ->assertSuccessful()
        ->assertDontSeeHtml('aria-pressed="true"');
})->with([[TestComponentWithToggleButtons::class], [RenderToggleButtonsWithGrouped::class]]);

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
    it('can set `size()` and get `getSize()`', function (): void {
        $toggleButtons = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->size(Size::Small);

        expect($toggleButtons->getSize())->toBe(Size::Small);
    });

    it('can set `size()` with a `Closure`', function (): void {
        $toggleButtons = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->size(static fn (): Size => Size::Small);

        expect($toggleButtons->getSize())->toBe(Size::Small);
    });

    it('normalizes string sizes returned by a `size()` `Closure`', function (): void {
        $toggleButtons = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->size(static fn (): string => 'sm');

        expect($toggleButtons->getSize())->toBe(Size::Small);
    });

    it('preserves custom string sizes returned by `getSize()`', function (): void {
        $toggleButtons = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->size('custom-size');

        expect($toggleButtons->getSize())->toBe('custom-size');
    });

    it('returns `Size::Medium` from `getSize()` by default', function (): void {
        $toggleButtons = ToggleButtons::make('status')->options(['a' => 'A']);

        expect($toggleButtons->getSize())->toBe(Size::Medium);
    });

    it('can reset `size()` to its default', function (): void {
        $toggleButtons = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->size(Size::Small)
            ->size(null);

        expect($toggleButtons->getSize())->toBe(Size::Medium);
    });

    it('uses the default button size when a `size()` `Closure` returns `null`', function (): void {
        $toggleButtons = ToggleButtons::make('status')
            ->options(['a' => 'A'])
            ->size(static fn (): null => null);

        expect($toggleButtons->getSize())->toBe(Size::Medium);
    });

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
    it('renders the medium button size by default', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active']),
            ])
            ->fill();

        expect($field->toHtml())->toContain('fi-size-md');
    });

    it('renders the configured button size', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->size(Size::Small),
            ])
            ->fill();

        expect($field->toHtml())
            ->toContain('fi-size-sm')
            ->not->toContain('fi-size-md');
    });

    it('renders the configured button size when grouped', function (): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->size(Size::Small)
                    ->grouped(),
            ])
            ->fill();

        expect($field->toHtml())
            ->toContain('fi-size-sm')
            ->not->toContain('fi-size-md');
    });

    it('renders a configured string button size', function (bool $isGrouped): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->size('sm')
                    ->grouped($isGrouped),
            ])
            ->fill();

        expect($field->toHtml())
            ->toContain('fi-size-sm')
            ->not->toContain('class="fi-btn sm');
    })->with([
        'ungrouped' => false,
        'grouped' => true,
    ]);

    it('escapes custom string button sizes', function (bool $isGrouped): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->size('custom-size" data-injected="yes')
                    ->grouped($isGrouped),
            ])
            ->fill();

        expect($field->toHtml())
            ->toContain('custom-size&quot; data-injected=&quot;yes')
            ->not->toContain('data-injected="yes"');
    })->with([
        'ungrouped' => false,
        'grouped' => true,
    ]);

    it('renders the expected icon size for each button size', function (bool $isGrouped, Size $size, string $expectedIconSize): void {
        Schema::make($livewire = Livewire::make())
            ->statePath('data')
            ->components([
                $field = ToggleButtons::make('status')
                    ->options(['active' => 'Active'])
                    ->icons(['active' => Heroicon::Check])
                    ->size($size)
                    ->grouped($isGrouped),
            ])
            ->fill();

        expect($field->toHtml())
            ->toContain("fi-icon fi-size-{$expectedIconSize}");
    })->with([
        'ungrouped extra small' => [false, Size::ExtraSmall, 'sm'],
        'grouped extra small' => [true, Size::ExtraSmall, 'sm'],
        'ungrouped small' => [false, Size::Small, 'sm'],
        'grouped small' => [true, Size::Small, 'sm'],
        'ungrouped medium' => [false, Size::Medium, 'md'],
        'grouped medium' => [true, Size::Medium, 'md'],
    ]);

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
});

it('can select, replace, and clear a single `ToggleButtons` option with pointer and keyboard', function (): void {
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $darkMode) {
        $page = $darkMode
            ? visit('/toggle-buttons-test')->inDarkMode()
            : visit('/toggle-buttons-test')->inLightMode();
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
        $livewireComponent = 'Livewire.find(document.getElementById("form.field-a").closest("[wire\\\\:id]").getAttribute("wire:id"))';

        foreach (['field', 'grouped_field'] as $fieldName) {
            $firstButton = "[id=\"form.{$fieldName}-a\"]";
            $secondButton = "[id=\"form.{$fieldName}-b\"]";

            $page->assertAttribute($firstButton, 'aria-pressed', 'false')
                ->click($firstButton)
                ->assertAttribute($firstButton, 'aria-pressed', 'true')
                ->click($secondButton)
                ->assertAttribute($firstButton, 'aria-pressed', 'false')
                ->assertAttribute($secondButton, 'aria-pressed', 'true');

            expect($page->script("{$livewireComponent}.call('save')")[$fieldName])->toBe('b');

            $page->click($secondButton)
                ->assertAttribute($secondButton, 'aria-pressed', 'false')
                ->keys($firstButton, 'Space')
                ->assertAttribute($firstButton, 'aria-pressed', 'true')
                ->keys($firstButton, 'Tab');

            expect($page->script('document.activeElement.id'))->toBe("form.{$fieldName}-b");

            $page->keys($secondButton, 'Enter')
                ->assertAttribute($firstButton, 'aria-pressed', 'false')
                ->assertAttribute($secondButton, 'aria-pressed', 'true')
                ->keys($secondButton, 'Space')
                ->assertAttribute($secondButton, 'aria-pressed', 'false');
        }

        $page->assertAttribute('[id="form.boolean_field-0"]', 'aria-pressed', 'true')
            ->keys('[id="form.boolean_field-0"]', 'Enter')
            ->assertAttribute('[id="form.boolean_field-0"]', 'aria-pressed', 'false')
            ->assertDisabled('[id="form.disabled_options-b"]')
            ->keys('[id="form.disabled_options-c"]', 'Enter')
            ->assertAttribute('[id="form.disabled_options-c"]', 'aria-pressed', 'false')
            ->click('[for="form.multiple_field-a"]')
            ->click('[for="form.multiple_field-b"]')
            ->assertChecked('[id="form.multiple_field-a"]')
            ->assertChecked('[id="form.multiple_field-b"]')
            ->click('[for="form.multiple_field-a"]')
            ->assertNotChecked('[id="form.multiple_field-a"]')
            ->assertChecked('[id="form.multiple_field-b"]');

        $state = $page->script("{$livewireComponent}.call('save')");

        expect($state)->toMatchArray([
            'field' => null,
            'grouped_field' => null,
            'boolean_field' => null,
            'disabled_options' => null,
            'multiple_field' => ['b'],
        ]);
    }
});

it('preserves `live()` update timing and submission locking for single `ToggleButtons`', function (): void {
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $darkMode) {
        $page = $darkMode
            ? visit('/toggle-buttons-test')->inDarkMode()
            : visit('/toggle-buttons-test')->inLightMode();
        $livewireComponent = 'Livewire.find(document.getElementById("form.field-a").closest("[wire\\\\:id]").getAttribute("wire:id"))';

        foreach (['deferred_blur', 'deferred_debounced', 'deferred_override'] as $fieldName) {
            $button = "[id=\"form.{$fieldName}-a\"]";

            $page->click($button)
                ->assertAttribute($button, 'aria-pressed', 'true')
                ->keys($button, 'Tab')
                ->wait(0.5)
                ->assertScript("{$livewireComponent}.get('updatedStates.{$fieldName}') ?? []", []);
        }

        $page->click('[id="form.live_field-a"]')
            ->assertScript("{$livewireComponent}.get('updatedStates.live_field')", ['a'])
            ->click('[id="form.live_field-a"]')
            ->assertScript("{$livewireComponent}.get('updatedStates.live_field')", ['a', null]);

        foreach (['blur', 'grouped_blur'] as $fieldName) {
            $button = "[id=\"form.{$fieldName}-a\"]";
            $updates = "{$livewireComponent}.get('updatedStates.{$fieldName}') ?? []";

            $page->script("document.getElementById('form.{$fieldName}-a').focus()");
            $page->script("{$livewireComponent}.\$commit()");

            $page->click($button)
                ->assertAttribute($button, 'aria-pressed', 'true')
                ->wait(0.2)
                ->assertScript($updates, [])
                ->keys($button, 'Tab')
                ->assertScript($updates, ['a']);
        }

        foreach (['debounced', 'grouped_debounced'] as $fieldName) {
            $firstButton = "[id=\"form.{$fieldName}-a\"]";
            $secondButton = "[id=\"form.{$fieldName}-b\"]";
            $updates = "{$livewireComponent}.get('updatedStates.{$fieldName}') ?? []";

            $page->script("document.getElementById('form.{$fieldName}-a').focus()");
            $page->script("{$livewireComponent}.\$commit()");

            $page->click($firstButton)
                ->assertAttribute($firstButton, 'aria-pressed', 'true')
                ->wait(0.2)
                ->assertScript($updates, [])
                ->click($secondButton)
                ->assertAttribute($firstButton, 'aria-pressed', 'false')
                ->assertAttribute($secondButton, 'aria-pressed', 'true')
                ->wait(0.6)
                ->assertScript($updates, [])
                ->assertEnabled($firstButton)
                ->assertScript($updates, ['b']);
        }

        $submissionDisabled = $page->script('(async () => { document.querySelector("[data-testid=save]").click(); return new Promise(resolve => setTimeout(() => resolve([document.getElementById("form.field-a").disabled, document.getElementById("form.grouped_field-a").disabled]), 50)); })()');

        expect($submissionDisabled)->toBe([true, true]);

        $page->assertEnabled('[id="form.field-a"]')
            ->assertEnabled('[id="form.grouped_field-a"]')
            ->assertDisabled('[id="form.disabled_options-b"]')
            ->wait(0.2)
            ->assertNoAccessibilityIssues();
    }
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
