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

describe('submit on completion', function (): void {
    it('defaults `shouldSubmitOnCompletion()` to `false`', function (): void {
        $input = OneTimeCodeInput::make('code');

        expect($input->shouldSubmitOnCompletion())->toBeFalse();
    });

    it('can set `submitOnCompletion()`', function (): void {
        $input = OneTimeCodeInput::make('code')->submitOnCompletion();

        expect($input->shouldSubmitOnCompletion())->toBeTrue();
    });

    it('can set `submitOnCompletion()` with a `Closure`', function (): void {
        $input = OneTimeCodeInput::make('code')
            ->submitOnCompletion(static fn (): bool => true);

        expect($input->shouldSubmitOnCompletion())->toBeTrue();
    });

    it('can render with `submitOnCompletion()`', function (): void {
        livewire(RenderOneTimeCodeInputWithSubmitOnCompletion::class)
            ->assertSuccessful()
            ->assertSeeHtml('shouldSubmitOnCompletion: true');
    });

    it('does not enable submitting on completion when rendered by default', function (): void {
        livewire(TestComponentWithOneTimeCodeInput::class)
            ->assertSeeHtml('shouldSubmitOnCompletion: false');
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

it('handles code entry in left-to-right and right-to-left layouts and commits the code to the Livewire state', function (bool $isRightToLeft): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/one-time-code-input-browser-test?rtl=' . (int) $isRightToLeft);

    $page
        ->type('[role="group"] input[type="text"]:nth-child(1)', '1234567')
        ->assertValue('[role="group"] input[type="text"]:nth-child(1)', '1')
        ->assertValue('[role="group"] input[type="text"]:nth-child(2)', '2')
        ->assertValue('[role="group"] input[type="text"]:nth-child(5)', '5')
        ->fill('[role="group"] input[type="text"]:nth-child(1)', 'a6b5c4d3e2f1')
        ->assertValue('[role="group"] input[type="text"]:nth-child(1)', '6')
        ->assertValue('[role="group"] input[type="text"]:nth-child(2)', '5')
        ->assertValue('[role="group"] input[type="text"]:nth-child(3)', '4')
        ->assertValue('[role="group"] input[type="text"]:nth-child(4)', '3')
        ->assertValue('[role="group"] input[type="text"]:nth-child(5)', '2')
        ->assertValue('[role="group"] input[type="text"]:nth-child(6)', '1')
        ->fill('[role="group"] input[type="text"]:nth-child(1)', '123456')
        ->assertValue('[role="group"] input[type="text"]:nth-child(1)', '1')
        ->assertValue('[role="group"] input[type="text"]:nth-child(2)', '2')
        ->assertValue('[role="group"] input[type="text"]:nth-child(3)', '3')
        ->assertValue('[role="group"] input[type="text"]:nth-child(4)', '4')
        ->assertValue('[role="group"] input[type="text"]:nth-child(5)', '5')
        ->assertValue('[role="group"] input[type="text"]:nth-child(6)', '6')
        ->assertSeeIn('[data-testid="submission-attempt-count"]', '0')
        ->press('Save')
        ->assertSeeIn('[data-testid="submitted-code"]', '123456')
        ->assertSeeIn('[data-testid="submission-attempt-count"]', '1');

    $page
        ->assertScript('document.querySelector(\'[role="group"] input[type="text"]:nth-child(1)\').getBoundingClientRect().left < document.querySelector(\'[role="group"] input[type="text"]:nth-child(2)\').getBoundingClientRect().left')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->inDarkMode()->assertNoAccessibilityIssues();
})->with([false, true]);

it('submits completed codes after correcting native validation errors with `submitOnCompletion()`', function (): void {
    $this->actingAs(User::factory()->create());

    $browser = visit('/one-time-code-input-submit-on-completion-browser-test');

    foreach ([$browser, $browser->inDarkMode()] as $page) {
        $page->fill('[data-testid="required-sibling"]', '')
            ->type('[data-testid="code-input"] input:nth-child(1)', '12345')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '0')
            ->fill('[data-testid="code-input"] input:nth-child(6)', '6')
            ->assertPresent('[data-testid="required-sibling"]:invalid')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '0')
            ->fill('[data-testid="required-sibling"]', 'Ada Lovelace')
            ->fill('[data-testid="code-input"] input:nth-child(6)', '6')
            ->assertSeeIn('[data-testid="submitted-code"]', '123456')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1')
            ->click('[data-testid="reset-code"]')
            ->assertValue('[data-testid="code-input"] input:nth-child(1)', '')
            ->fill('[data-testid="code-input"] input:nth-child(1)', '654321')
            ->assertSeeIn('[data-testid="submitted-code"]', '654321')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '2')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});

it('honors `novalidate` when using `submitOnCompletion()`', function (): void {
    $this->actingAs(User::factory()->create());

    foreach ([
        visit('/one-time-code-input-submit-on-completion-browser-test?novalidate=1'),
        visit('/one-time-code-input-submit-on-completion-browser-test?novalidate=1')->inDarkMode(),
    ] as $page) {
        $page->fill('[data-testid="required-sibling"]', '')
            ->fill('[data-testid="code-input"] input:nth-child(1)', '654321')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1')
            ->assertPresent('[data-validation-error]')
            ->type('[data-testid="code-input"] input:nth-child(6)', '1')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
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

class RenderOneTimeCodeInputWithSubmitOnCompletion extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([OneTimeCodeInput::make('code')->submitOnCompletion()])->statePath('data');
    }
}
