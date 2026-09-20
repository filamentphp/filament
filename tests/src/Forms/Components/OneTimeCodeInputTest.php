<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\OneTimeCodeInput;
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
    livewire(TestComponentWithOneTimeCodeInput::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithOneTimeCodeInput::class)
        ->fillForm(['code' => '123456'])
        ->assertSchemaStateSet(['code' => '123456']);
});

it('validates numeric input', function (): void {
    livewire(TestComponentWithOneTimeCodeInputValidation::class)
        ->fillForm(['code' => 'abcdef'])
        ->call('save')
        ->assertHasFormErrors(['code']);
});

it('validates digit length', function (): void {
    livewire(TestComponentWithOneTimeCodeInputValidation::class)
        ->fillForm(['code' => '123'])
        ->call('save')
        ->assertHasFormErrors(['code']);
});

it('passes validation with valid code', function (): void {
    livewire(TestComponentWithOneTimeCodeInputValidation::class)
        ->fillForm(['code' => '123456'])
        ->call('save')
        ->assertHasNoFormErrors();
});

describe('length', function (): void {
    it('defaults `getLength()` to `6`', function (): void {
        $input = OneTimeCodeInput::make('code');

        expect($input->getLength())->toBe(6);
    });

    it('can set `length()`', function (): void {
        $input = OneTimeCodeInput::make('code')->length(4);

        expect($input->getLength())->toBe(4);
    });

    it('can set `length()` with a `Closure`', function (): void {
        $input = OneTimeCodeInput::make('code')
            ->length(static fn (): int => 8);

        expect($input->getLength())->toBe(8);
    });
});

describe('custom length validation', function (): void {
    it('validates against custom `length()` of `4`', function (): void {
        livewire(TestComponentWithFourDigitCodeInput::class)
            ->fillForm(['code' => '1234'])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('fails validation when code does not match custom `length()` of `4`', function (): void {
        livewire(TestComponentWithFourDigitCodeInput::class)
            ->fillForm(['code' => '123456'])
            ->call('save')
            ->assertHasFormErrors(['code']);
    });
});

describe('read only', function (): void {
    it('defaults `isReadOnly()` to `false`', function (): void {
        $input = OneTimeCodeInput::make('code');

        expect($input->isReadOnly())->toBeFalse();
    });

    it('can set `readOnly()`', function (): void {
        $input = OneTimeCodeInput::make('code')->readOnly();

        expect($input->isReadOnly())->toBeTrue();
    });

    it('can set `readOnly()` with a `Closure`', function (): void {
        $input = OneTimeCodeInput::make('code')
            ->readOnly(static fn (): bool => true);

        expect($input->isReadOnly())->toBeTrue();
    });
});

describe('placeholder', function (): void {
    it('returns `null` for `getPlaceholder()` by default', function (): void {
        $input = OneTimeCodeInput::make('code');

        expect($input->getPlaceholder())->toBeNull();
    });

    it('can set `placeholder()`', function (): void {
        $input = OneTimeCodeInput::make('code')
            ->placeholder('0');

        expect($input->getPlaceholder())->toBe('0');
    });

    it('can set `placeholder()` with a `Closure`', function (): void {
        $input = OneTimeCodeInput::make('code')
            ->placeholder(static fn (): string => '•');

        expect($input->getPlaceholder())->toBe('•');
    });
});

describe('rendering', function (): void {
    it('can render with `length()` set via `Closure`', function (): void {
        livewire(RenderOneTimeCodeInputWithClosureLength::class)
            ->assertSuccessful();
    });

    it('can render with `readOnly()`', function (): void {
        livewire(RenderOneTimeCodeInputWithReadOnly::class)
            ->assertSuccessful();
    });

    it('can render with `readOnly()` set via `Closure`', function (): void {
        livewire(RenderOneTimeCodeInputWithClosureReadOnly::class)
            ->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderOneTimeCodeInputWithPlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `placeholder()` set via `Closure`', function (): void {
        livewire(RenderOneTimeCodeInputWithClosurePlaceholder::class)
            ->assertSuccessful();
    });
});

it('handles code entry and commits the code to the Livewire state', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/one-time-code-input-browser-test')
            ->type('.fi-one-time-code-input-ctn input:nth-child(1)', '1234567')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(1)', '1')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(2)', '2')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(5)', '5')
            ->fill('.fi-one-time-code-input-ctn input:nth-child(1)', 'a6b5c4d3e2f1')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(1)', '6')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(2)', '5')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(3)', '4')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(4)', '3')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(5)', '2')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(6)', '1')
            ->fill('.fi-one-time-code-input-ctn input:nth-child(1)', '123456')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(1)', '1')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(2)', '2')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(3)', '3')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(4)', '4')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(5)', '5')
            ->assertValue('.fi-one-time-code-input-ctn input:nth-child(6)', '6')
            ->press('Save')
            ->wait(1)
            ->assertSeeIn('[data-testid="submitted-code"]', '123456')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/one-time-code-input-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class TestComponentWithFourDigitCodeInput extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                OneTimeCodeInput::make('code')->length(4),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithOneTimeCodeInput extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                OneTimeCodeInput::make('code'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithOneTimeCodeInputValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                OneTimeCodeInput::make('code'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class RenderOneTimeCodeInputWithClosureLength extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([OneTimeCodeInput::make('code')->length(static fn (): int => 8)])->statePath('data');
    }
}

class RenderOneTimeCodeInputWithReadOnly extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([OneTimeCodeInput::make('code')->readOnly()])->statePath('data');
    }
}

class RenderOneTimeCodeInputWithClosureReadOnly extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([OneTimeCodeInput::make('code')->readOnly(static fn (): bool => true)])->statePath('data');
    }
}

class RenderOneTimeCodeInputWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([OneTimeCodeInput::make('code')->placeholder('0')])->statePath('data');
    }
}

class RenderOneTimeCodeInputWithClosurePlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([OneTimeCodeInput::make('code')->placeholder(static fn (): string => '•')])->statePath('data');
    }
}
