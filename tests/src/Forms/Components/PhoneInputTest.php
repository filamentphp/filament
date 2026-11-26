<?php

use Filament\Forms\Components\PhoneInput;
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

describe('configuration', function (): void {
    it('returns `null` for `getCountries()` and `getDefaultCountry()` by default', function (): void {
        $input = PhoneInput::make('phone');

        expect($input->getCountries())->toBeNull()
            ->and($input->getDefaultCountry())->toBeNull()
            ->and($input->getPreferredCountries())->toBe([]);
    });

    it('can set `countries()`, `defaultCountry()` and `preferredCountries()` with a `Closure`', function (): void {
        $input = PhoneInput::make('phone')
            ->countries(fn (): array => ['BR', 'US', 'PT'])
            ->defaultCountry(fn (): string => 'BR')
            ->preferredCountries(fn (): array => ['BR']);

        expect($input->getCountries())->toBe(['BR', 'US', 'PT'])
            ->and($input->getDefaultCountry())->toBe('BR')
            ->and($input->getPreferredCountries())->toBe(['BR']);
    });

    it('returns the app locale from `getLocale()` by default', function (): void {
        expect(PhoneInput::make('phone')->getLocale())->toBe(config('app.locale'))
            ->and(PhoneInput::make('phone')->locale(fn (): string => 'pt_BR')->getLocale())->toBe('pt_BR');
    });

    it('returns the E.164 pattern from `getTelRegex()` by default', function (): void {
        expect(PhoneInput::make('phone')->getTelRegex())->toBe('/^\+[1-9][0-9]{1,14}$/')
            ->and(PhoneInput::make('phone')->telRegex(fn (): string => '/^\+55[0-9]{10,11}$/')->getTelRegex())->toBe('/^\+55[0-9]{10,11}$/');
    });

    it('can set `copyable()`', function (): void {
        expect(PhoneInput::make('phone')->isCopyable())->toBeFalse()
            ->and(PhoneInput::make('phone')->copyable()->isCopyable())->toBeTrue();
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(TestComponentWithPhoneInput::class)
            ->assertSuccessful()
            ->assertSeeHtml([
                'phoneInputFormComponent(',
                'countries: null',
                'defaultCountry: null',
                'locale: \'en\'',
                'preferredCountries: []',
                'type="tel"',
            ]);
    });

    it('can render with every option', function (): void {
        livewire(RenderPhoneInputWithEveryOption::class)
            ->assertSuccessful()
            ->assertSeeHtml([
                'countries: JSON.parse(\'[\\u0022BR\\u0022,\\u0022US\\u0022,\\u0022PT\\u0022]\')',
                'defaultCountry: \'BR\'',
                'preferredCountries: JSON.parse(\'[\\u0022BR\\u0022]\')',
                'locale: \'pt_BR\'',
                'placeholder="Your number"',
                'autocomplete="tel"',
            ]);
    });

    it('cannot change the country when disabled or read-only', function (string $component): void {
        livewire($component)
            ->assertSuccessful()
            ->assertDontSeeHtml('x-on:click="togglePanelVisibility()"');
    })->with([
        'disabled' => [RenderDisabledPhoneInput::class],
        'read-only' => [RenderReadOnlyPhoneInput::class],
    ]);

    it('adds a copy action with `copyable()`', function (): void {
        livewire(TestComponentWithPhoneInput::class)
            ->assertDontSeeHtml('writeText');

        livewire(RenderCopyablePhoneInput::class)
            ->assertSeeHtml('writeText');
    });

    it('escapes what it prints', function (): void {
        livewire(RenderPhoneInputWithUnsafeOptions::class)
            ->assertDontSeeHtml(['<script>alert(1)</script>', '\'});alert(2)']);
    });
});

describe('state and validation', function (): void {
    it('saves a number in the E.164 format', function (): void {
        livewire(TestComponentWithPhoneInput::class)
            ->set('data.phone', '+5511987654321')
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.phone', '+5511987654321');
    });

    it('saves `null` without a number', function (): void {
        livewire(TestComponentWithPhoneInput::class)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.phone', null);
    });

    it('rejects a number that is not in the E.164 format', function (string $state): void {
        livewire(TestComponentWithPhoneInput::class)
            ->set('data.phone', $state)
            ->call('save')
            ->assertHasFormErrors(['phone' => 'regex']);
    })->with([
        'national' => ['(11) 98765-4321'],
        'no plus' => ['5511987654321'],
        'formatted' => ['+55 11 98765-4321'],
        'too long' => ['+1234567890123456'],
        'letters' => ['+55abc'],
    ]);

    it('validates with `telRegex()`', function (): void {
        livewire(TestComponentWithPhoneInput::class, ['telRegex' => '/^\+55[0-9]{10,11}$/'])
            ->set('data.phone', '+12015550123')
            ->call('save')
            ->assertHasFormErrors(['phone' => 'regex'])
            ->set('data.phone', '+5511987654321')
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

describe('browser interactions', function (): void {
    it('formats a number as it is typed and saves it in the E.164 format', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/phone-input-browser-test')->waitForEvent('networkidle');

        $page->assertNoSmoke()
            ->assertAttribute('[aria-controls="form.phone-panel"]', 'aria-label', 'Country: Brazil (+55)')
            ->type('[data-testid="phone"]', '11987654321')
            ->assertValue('[data-testid="phone"]', '(11) 98765-4321')
            ->click('[data-testid="save-phones"]')
            ->assertScript('document.querySelector(\'[data-testid="saved-phone"]\').textContent.trim()', '+5511987654321');
    });

    it('selects the country of a number typed with its calling code', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/phone-input-browser-test')->waitForEvent('networkidle');

        $page->assertNoSmoke()
            ->type('[data-testid="phone"]', '+12015550123')
            ->assertAttribute('[aria-controls="form.phone-panel"]', 'aria-label', 'Country: United States (+1)')
            ->click('[data-testid="save-phones"]')
            ->assertScript('document.querySelector(\'[data-testid="saved-phone"]\').textContent.trim()', '+12015550123')
            ->assertValue('[data-testid="phone"]', '(201) 555-0123');
    });

    it('chooses a country from the searchable list with the keyboard', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/phone-input-browser-test')->waitForEvent('networkidle');

        $panel = '[id="form.phone-panel"]';

        $page->assertNoSmoke()
            ->click('[aria-controls="form.phone-panel"]')
            ->assertAttribute('[aria-controls="form.phone-panel"]', 'aria-expanded', 'true')
            ->assertPresent("{$panel} input[role=\"combobox\"]:focus")
            ->assertNoAccessibilityIssues()
            ->type("{$panel} input[role=\"combobox\"]", 'portug')
            ->assertAttribute("{$panel} input[role=\"combobox\"]", 'aria-activedescendant', 'form.phone-panel-PT')
            ->keys("{$panel} input[role=\"combobox\"]", 'Enter')
            ->assertPresent('[data-testid="phone"]:focus')
            ->assertAttribute('[aria-controls="form.phone-panel"]', 'aria-label', 'Country: Portugal (+351)')
            ->assertAttribute('[aria-controls="form.phone-panel"]', 'aria-expanded', 'false');
    });

    it('announces when no country matches the search, stays open when the message is clicked, and closes on Escape', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/phone-input-browser-test');

        $panel = '[id="form.phone-panel"]';

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[aria-controls="form.phone-panel"]')
            ->type("{$panel} input[role=\"combobox\"]", 'zzz')
            ->assertScript('document.querySelector(\'[id="form.phone-panel"] [role="status"]\').textContent.trim()', 'No countries match your search.')
            ->click("{$panel} .fi-fo-phone-input-message")
            ->wait(0.5)
            ->assertAttribute('[aria-controls="form.phone-panel"]', 'aria-expanded', 'true')
            ->assertPresent("{$panel} input[role=\"combobox\"]:focus")
            ->keys("{$panel} input[role=\"combobox\"]", 'Escape')
            ->assertPresent('[aria-controls="form.phone-panel"]:focus')
            ->assertAttribute('[aria-controls="form.phone-panel"]', 'aria-expanded', 'false');

        $page->inDarkMode()
            ->waitForEvent('networkidle')
            ->click('[aria-controls="form.phone-panel"]')
            ->assertVisible($panel)
            ->assertNoAccessibilityIssues();
    });
});

class TestComponentWithPhoneInput extends Livewire
{
    public ?string $telRegex = null;

    public array $saved = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->telRegex($this->telRegex),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
    }
}

class RenderPhoneInputWithEveryOption extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->countries(fn (): array => ['BR', 'US', 'PT'])
                    ->defaultCountry(fn (): string => 'BR')
                    ->preferredCountries(['BR'])
                    ->locale('pt_BR')
                    ->placeholder('Your number')
                    ->autocomplete('tel'),
            ])
            ->statePath('data');
    }
}

class RenderDisabledPhoneInput extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->disabled(),
            ])
            ->statePath('data');
    }
}

class RenderReadOnlyPhoneInput extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->readOnly(),
            ])
            ->statePath('data');
    }
}

class RenderCopyablePhoneInput extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->copyable(),
            ])
            ->statePath('data');
    }
}

class RenderPhoneInputWithUnsafeOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->placeholder('"><script>alert(1)</script>')
                    ->defaultCountry('\'});alert(2);({a:\''),
            ])
            ->statePath('data');
    }
}
