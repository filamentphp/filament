<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Radio;
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
    livewire(TestComponentWithRadio::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithRadio::class)
        ->fillForm(['status' => 'active'])
        ->assertSchemaStateSet(['status' => 'active']);
});

it('can render inline', function (): void {
    livewire(TestComponentWithInlineRadio::class)
        ->assertSuccessful();
});

it('can render boolean mode', function (): void {
    livewire(TestComponentWithBooleanRadio::class)
        ->assertSuccessful();
});

it('can set boolean state', function (): void {
    livewire(TestComponentWithBooleanRadio::class)
        ->fillForm(['is_active' => 1])
        ->assertSchemaStateSet(['is_active' => true]);
});

it('can set `boolean()` options', function (): void {
    $radio = Radio::make('active')
        ->boolean();

    $options = $radio->getOptions();

    expect($options)->toHaveCount(2);
    expect($options[1])->toBe('Yes');
    expect($options[0])->toBe('No');
});

it('can set custom labels for `boolean()`', function (): void {
    $radio = Radio::make('active')
        ->boolean(trueLabel: 'On', falseLabel: 'Off');

    $options = $radio->getOptions();

    expect($options[1])->toBe('On');
    expect($options[0])->toBe('Off');
});

it('can set `inline()`', function (): void {
    $radio = Radio::make('status');

    expect($radio->isInline())->toBeFalse();

    $radio->inline();

    expect($radio->isInline())->toBeTrue();
});

it('converts boolean default state to `int`', function (): void {
    $radio = Radio::make('active')
        ->default(true);

    expect($radio->getDefaultState())->toBe(1);

    $radio->default(false);

    expect($radio->getDefaultState())->toBe(0);
});

describe('validation', function (): void {
    it('automatically validates against options array', function (): void {
        livewire(TestComponentWithRadioValidation::class)
            ->fillForm(['status' => 'active'])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithRadioValidation::class)
            ->fillForm(['status' => 'archived'])
            ->call('save')
            ->assertHasFormErrors(['status' => ['in']]);
    });

    it('rejects disabled options during validation', function (): void {
        livewire(TestComponentWithDisabledRadioOption::class)
            ->fillForm(['status' => 'active'])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithDisabledRadioOption::class)
            ->fillForm(['status' => 'archived'])
            ->call('save')
            ->assertHasFormErrors(['status' => ['in']]);
    });

    it('passes validation when state is blank', function (): void {
        livewire(TestComponentWithRadioValidation::class)
            ->fillForm(['status' => null])
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

class TestComponentWithRadioValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('status')
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

class TestComponentWithDisabledRadioOption extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'archived' => 'Archived',
                    ])
                    ->disableOptionWhen(static fn (string $value): bool => $value === 'archived'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithRadio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithInlineRadio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->inline(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithBooleanRadio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('is_active')->boolean(),
            ])
            ->statePath('data');
    }
}

it('can set `inline()` with a `Closure`', function (): void {
    $radio = Radio::make('status')
        ->inline(static fn (): bool => true);

    expect($radio->isInline())->toBeTrue();
});

it('can undo `inline()` with `false`', function (): void {
    $radio = Radio::make('status')
        ->inline()
        ->inline(false);

    expect($radio->isInline())->toBeFalse();
});

it('returns `true` for `hasNullableBooleanState()`', function (): void {
    $radio = Radio::make('status');

    expect($radio->hasNullableBooleanState())->toBeTrue();
});

it('passes through non-boolean `getDefaultState()` unchanged', function (): void {
    $radio = Radio::make('status')
        ->default('active');

    expect($radio->getDefaultState())->toBe('active');
});

it('returns `null` for `getDefaultState()` when no default set', function (): void {
    $radio = Radio::make('status');

    expect($radio->getDefaultState())->toBeNull();
});

it('returns fluent `$this` from `boolean()`', function (): void {
    $radio = Radio::make('status');

    $result = $radio->boolean();

    expect($result)->toBe($radio);
});

describe('disabled options', function (): void {
    it('can disable specific options with `disableOptionWhen()`', function (): void {
        $radio = Radio::make('status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ])
            ->disableOptionWhen(static fn (string $value): bool => $value === 'archived');

        $enabled = $radio->getEnabledOptions();

        expect($enabled)->toBe([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
    });

    it('returns only enabled option keys from `getInValidationRuleValues()`', function (): void {
        $radio = Radio::make('status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ])
            ->disableOptionWhen(static fn (string $value): bool => $value === 'archived');

        $validValues = $radio->getInValidationRuleValues();

        expect($validValues)->toBe(['active', 'inactive']);
    });
});

describe('descriptions on Radio', function (): void {
    it('can set `descriptions()` and retrieve with `getDescription()`', function (): void {
        $radio = Radio::make('priority')
            ->options(['low' => 'Low', 'high' => 'High'])
            ->descriptions(['low' => 'Not urgent', 'high' => 'Urgent']);

        expect($radio->getDescription('low'))->toBe('Not urgent');
        expect($radio->getDescription('high'))->toBe('Urgent');
        expect($radio->hasDescription('low'))->toBeTrue();
        expect($radio->hasDescription('missing'))->toBeFalse();
    });
});

describe('rendering', function (): void {
    it('can render with `inline()` set via `Closure`', function (): void {
        livewire(RenderRadioWithClosureInline::class)
            ->assertSuccessful();
    });

    it('can render with `inline(false)` after `inline()`', function (): void {
        livewire(RenderRadioWithInlineUndone::class)
            ->assertSuccessful();
    });

    it('can render with `disableOptionWhen()`', function (): void {
        livewire(RenderRadioWithDisabledOption::class)
            ->assertSuccessful()
            ->assertSeeHtml('Active')
            ->assertSeeHtml('Archived');
    });

    it('can render with `descriptions()`', function (): void {
        livewire(RenderRadioWithDescriptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('Not urgent')
            ->assertSeeHtml('Urgent');
    });

    it('can render with custom `boolean()` labels', function (): void {
        livewire(RenderRadioWithCustomBooleanLabels::class)
            ->assertSuccessful()
            ->assertSeeHtml('On')
            ->assertSeeHtml('Off');
    });
});

it('can render `Radio` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/radio-test')
            ->assertSee('Test Radio')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/radio-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderRadioWithClosureInline extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Radio::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                ->inline(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderRadioWithInlineUndone extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Radio::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                ->inline()
                ->inline(false),
        ])->statePath('data');
    }
}

class RenderRadioWithDisabledOption extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Radio::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive', 'archived' => 'Archived'])
                ->disableOptionWhen(static fn (string $value): bool => $value === 'archived'),
        ])->statePath('data');
    }
}

class RenderRadioWithDescriptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Radio::make('priority')
                ->options(['low' => 'Low', 'high' => 'High'])
                ->descriptions(['low' => 'Not urgent', 'high' => 'Urgent']),
        ])->statePath('data');
    }
}

class RenderRadioWithCustomBooleanLabels extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Radio::make('active')
                ->boolean(trueLabel: 'On', falseLabel: 'Off'),
        ])->statePath('data');
    }
}
