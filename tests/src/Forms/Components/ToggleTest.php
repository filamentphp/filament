<?php

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can toggle state by clicking in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/toggle-test')
            ->assertSee('Toggle Test')
            ->assertSee('Basic Toggle')
            ->assertAttribute('[data-testid="toggle"]', 'aria-checked', 'false')
            ->click('[data-testid="toggle"]')
            ->assertAttribute('[data-testid="toggle"]', 'aria-checked', 'true')
            ->click('[data-testid="toggle"]')
            ->assertAttribute('[data-testid="toggle"]', 'aria-checked', 'false')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/toggle-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

it('can set and get `onColor()`', function (): void {
    $toggle = Toggle::make('active');
    expect($toggle->getOnColor())->toBeNull();
    $toggle->onColor('success');
    expect($toggle->getOnColor())->toBe('success');
});

it('can set and get `offColor()`', function (): void {
    $toggle = Toggle::make('active');
    expect($toggle->getOffColor())->toBeNull();
    $toggle->offColor('danger');
    expect($toggle->getOffColor())->toBe('danger');
});

it('can set and get `onIcon()`', function (): void {
    $toggle = Toggle::make('active');
    expect($toggle->getOnIcon())->toBeNull();
    $toggle->onIcon(Heroicon::OutlinedCheck);
    expect($toggle->getOnIcon())->toBe(Heroicon::OutlinedCheck);
});

it('can set and get `offIcon()`', function (): void {
    $toggle = Toggle::make('active');
    expect($toggle->getOffIcon())->toBeNull();
    $toggle->offIcon(Heroicon::OutlinedXMark);
    expect($toggle->getOffIcon())->toBe(Heroicon::OutlinedXMark);
});

it('can set `inline()`', function (): void {
    $toggle = Toggle::make('active')
        ->container(Schema::make(Livewire::make()));
    expect($toggle->isInline())->toBeTrue();
    $toggle->inline(false);
    expect($toggle->isInline())->toBeFalse();
});

it('can render a toggle with `onColor()`', function (): void {
    livewire(TestComponentWithColoredToggle::class)
        ->assertSuccessful();
});

it('can render a toggle with icons', function (): void {
    livewire(TestComponentWithIconToggle::class)
        ->assertSuccessful();
});

it('defaults state to `false`', function (): void {
    $toggle = Toggle::make('active');

    expect($toggle->getDefaultState())->toBeFalse();
});

describe('`accepted()` and `declined()` validation', function (): void {
    it('fails validation when `accepted()` toggle is off', function (): void {
        livewire(TestComponentWithAcceptedToggle::class)
            ->fillForm(['terms' => false])
            ->call('save')
            ->assertHasFormErrors(['terms' => ['accepted']]);
    });

    it('passes validation when `accepted()` toggle is on', function (): void {
        livewire(TestComponentWithAcceptedToggle::class)
            ->fillForm(['terms' => true])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('fails validation when `declined()` toggle is on', function (): void {
        livewire(TestComponentWithDeclinedToggle::class)
            ->fillForm(['opt_out' => true])
            ->call('save')
            ->assertHasFormErrors(['opt_out' => ['declined']]);
    });

    it('passes validation when `declined()` toggle is off', function (): void {
        livewire(TestComponentWithDeclinedToggle::class)
            ->fillForm(['opt_out' => false])
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

describe('Closure support', function (): void {
    it('can set `onColor()` with a `Closure`', function (): void {
        $toggle = Toggle::make('active')
            ->onColor(static fn (): string => 'success');

        expect($toggle->getOnColor())->toBe('success');
    });

    it('can set `offColor()` with a `Closure`', function (): void {
        $toggle = Toggle::make('active')
            ->offColor(static fn (): string => 'danger');

        expect($toggle->getOffColor())->toBe('danger');
    });

    it('can set `onIcon()` with a `Closure`', function (): void {
        $toggle = Toggle::make('active')
            ->onIcon(static fn () => Heroicon::OutlinedCheck);

        expect($toggle->getOnIcon())->toBe(Heroicon::OutlinedCheck);
    });

    it('can set `offIcon()` with a `Closure`', function (): void {
        $toggle = Toggle::make('active')
            ->offIcon(static fn () => Heroicon::OutlinedXMark);

        expect($toggle->getOffIcon())->toBe(Heroicon::OutlinedXMark);
    });

    it('can set `inline()` with a `Closure`', function (): void {
        $toggle = Toggle::make('active')
            ->container(Schema::make(Livewire::make()))
            ->inline(static fn (): bool => false);

        expect($toggle->isInline())->toBeFalse();
    });
});

describe('rendering', function (): void {
    it('can render with `onColor()` set via `Closure`', function (): void {
        livewire(RenderToggleWithClosureOnColor::class)->assertSuccessful();
    });

    it('can render with `offColor()` set via `Closure`', function (): void {
        livewire(RenderToggleWithClosureOffColor::class)->assertSuccessful();
    });

    it('can render with `onIcon()` set via `Closure`', function (): void {
        livewire(RenderToggleWithClosureOnIcon::class)->assertSuccessful();
    });

    it('can render with `offIcon()` set via `Closure`', function (): void {
        livewire(RenderToggleWithClosureOffIcon::class)->assertSuccessful();
    });

    it('can render with `inline(false)`', function (): void {
        livewire(RenderToggleWithInlineFalse::class)->assertSuccessful();
    });

    it('can render with `inline()` set via `Closure`', function (): void {
        livewire(RenderToggleWithClosureInline::class)->assertSuccessful();
    });
});

class TestComponentWithAcceptedToggle extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Toggle::make('terms')->accepted(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithDeclinedToggle extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Toggle::make('opt_out')->declined(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithColoredToggle extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Toggle::make('active')
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithIconToggle extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Toggle::make('active')
                    ->onIcon(Heroicon::OutlinedCheck)
                    ->offIcon(Heroicon::OutlinedXMark),
            ])
            ->statePath('data');
    }
}

class RenderToggleWithClosureOnColor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Toggle::make('active')->onColor(static fn (): string => 'success')])->statePath('data');
    }
}

class RenderToggleWithClosureOffColor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Toggle::make('active')->offColor(static fn (): string => 'danger')])->statePath('data');
    }
}

class RenderToggleWithClosureOnIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Toggle::make('active')->onIcon(static fn () => Heroicon::OutlinedCheck)])->statePath('data');
    }
}

class RenderToggleWithClosureOffIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Toggle::make('active')->offIcon(static fn () => Heroicon::OutlinedXMark)])->statePath('data');
    }
}

class RenderToggleWithInlineFalse extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Toggle::make('active')->inline(false)])->statePath('data');
    }
}

class RenderToggleWithClosureInline extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Toggle::make('active')->inline(static fn (): bool => false)])->statePath('data');
    }
}
