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

it('handles code entry in left-to-right and right-to-left layouts and commits the code to the Livewire state', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/one-time-code-input-browser-test');

        $page
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
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '0')
            ->press('Save')
            ->wait(1)
            ->assertSeeIn('[data-testid="submitted-code"]', '123456')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1');

        $page->script('document.documentElement.dir = \'rtl\'');

        $page
            ->assertScript('document.querySelector(\'.fi-one-time-code-input-ctn input:nth-child(1)\').getBoundingClientRect().left < document.querySelector(\'.fi-one-time-code-input-ctn input:nth-child(2)\').getBoundingClientRect().left')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        $darkModePage = visit('/one-time-code-input-browser-test')->inDarkMode();

        $darkModePage->script('document.documentElement.dir = \'rtl\'');

        $darkModePage->assertNoAccessibilityIssues();
    });
});

it('retries `submitOnCompletion()` after native validation fails and deduplicates submitted codes', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/one-time-code-input-submit-on-completion-browser-test')
            ->type('[data-testid="code-input"] input:nth-child(1)', '12345')
            ->wait(1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '0')
            ->fill('[data-testid="required-sibling"]', '');

        $page->script(<<<'JS'
            window.invalidEventCount = 0
            document.querySelector('[data-testid="required-sibling"]').addEventListener('invalid', () => window.invalidEventCount++)
            const input = document.querySelector('[data-testid="code-input"] input:nth-child(6)')
            input.value = '6'
            input.dispatchEvent(new InputEvent('input', { bubbles: true, data: '6', inputType: 'insertText' }))
            input.dispatchEvent(new InputEvent('input', { bubbles: true, data: '6', inputType: 'insertText' }))
            JS);

        $page
            ->wait(1)
            ->assertScript('window.invalidEventCount', 1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '0')
            ->fill('[data-testid="required-sibling"]', 'Ada Lovelace');

        // Replace the completed digit without clearing it, which would reset deduplication.
        $page->script(<<<'JS'
            const input = document.querySelector('[data-testid="code-input"] input:nth-child(6)')
            input.value = '6'
            input.dispatchEvent(new InputEvent('input', { bubbles: true, data: '6', inputType: 'insertText' }))
            input.dispatchEvent(new InputEvent('input', { bubbles: true, data: '6', inputType: 'insertText' }))
            JS);

        $page
            ->wait(1)
            ->assertSeeIn('[data-testid="submitted-code"]', '123456')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1')
            ->type('[data-testid="code-input"] input:nth-child(6)', '6')
            ->wait(1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1')
            ->fill('[data-testid="code-input"] input:nth-child(6)', '')
            ->type('[data-testid="code-input"] input:nth-child(6)', '6')
            ->wait(1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '2')
            ->click('[data-testid="reset-code"]')
            ->wait(1)
            ->assertValue('[data-testid="code-input"] input:nth-child(1)', '')
            ->fill('[data-testid="code-input"] input:nth-child(1)', '123456')
            ->wait(1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '3');

        $page->script(<<<'JS'
            const input = document.querySelector('[data-testid="code-input"] input:nth-child(6)')
            input.value = '7'
            input.dispatchEvent(new InputEvent('input', { bubbles: true, data: '7', inputType: 'insertText' }))
            input.value = '6'
            input.dispatchEvent(new InputEvent('input', { bubbles: true, data: '6', inputType: 'insertText' }))
            JS);

        $page
            ->wait(1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '3')
            ->type('[data-testid="code-input"] input:nth-child(6)', '7')
            ->wait(1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '4')
            ->assertSeeIn('[data-testid="submitted-code"]', '123457')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/one-time-code-input-submit-on-completion-browser-test')
            ->inDarkMode()
            ->fill('[data-testid="code-input"] input:nth-child(1)', '654321')
            ->wait(1)
            ->assertSeeIn('[data-testid="submitted-code"]', '654321')
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1')
            ->assertNoAccessibilityIssues();
    });
});

it('honors `novalidate` when using `submitOnCompletion()`', function (): void {
    $this->actingAs(User::factory()->create());

    foreach ([
        visit('/one-time-code-input-submit-on-completion-browser-test'),
        visit('/one-time-code-input-submit-on-completion-browser-test')->inDarkMode(),
    ] as $page) {
        $page->fill('[data-testid="required-sibling"]', '');

        $page->script(<<<'JS'
        const input = document.querySelector('[data-testid="required-sibling"]')
        input.form.noValidate = true
        window.invalidEventCount = 0
        input.addEventListener('invalid', () => window.invalidEventCount++)
        JS);

        $page
            ->fill('[data-testid="code-input"] input:nth-child(1)', '654321')
            ->wait(1)
            ->assertSeeIn('[data-testid="submission-attempt-count"]', '1')
            ->assertScript('window.invalidEventCount', 0)
            ->type('[data-testid="code-input"] input:nth-child(6)', '1')
            ->wait(1)
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
