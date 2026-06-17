<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can `trim()` whitespace from `TextInput`', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithTextInputTrim::class)
        ->fillForm(['name' => $input])
        ->call('save')
        ->assertSet('data.name', $expected);
})->with([
    ['  test value  ', 'test value'],
    ['test value', 'test value'],
    [null, null],
    ['', ''],
    [123, 123],
    ["\t test value \n", 'test value'],
    ['　test value　', 'test value'],
    [" \t　test value　\n ", 'test value'],
    ['   ', ''],
    ["\t\n　", ''],
]);

it('can strip characters before applying `numeric()` state cast', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithNumericAndStripCharacters::class)
        ->fillForm(['price' => $input])
        ->call('save')
        ->assertSet('data.price', $expected);
})->with([
    ['1,234.56', 1234.56],
    ['1,234,567.89', 1234567.89],
    ['999.99', 999.99],
    ['1,000', 1000.0],
    [null, null],
]);

it('can set `email()`', function (): void {
    $input = TextInput::make('email');

    expect($input->isEmail())->toBeFalse();
    expect($input->getType())->toBe('text');

    $input->email();

    expect($input->isEmail())->toBeTrue();
    expect($input->getType())->toBe('email');
});

it('can set `numeric()`', function (): void {
    $input = TextInput::make('amount');

    expect($input->isNumeric())->toBeFalse();

    $input->numeric();

    expect($input->isNumeric())->toBeTrue();
    expect($input->getType())->toBe('number');
});

it('can set `password()`', function (): void {
    $input = TextInput::make('secret');

    expect($input->isPassword())->toBeFalse();

    $input->password();

    expect($input->isPassword())->toBeTrue();
    expect($input->getType())->toBe('password');
});

it('can set `tel()`', function (): void {
    $input = TextInput::make('phone');

    expect($input->isTel())->toBeFalse();

    $input->tel();

    expect($input->isTel())->toBeTrue();
    expect($input->getType())->toBe('tel');
});

it('can set `url()`', function (): void {
    $input = TextInput::make('website');

    expect($input->isUrl())->toBeFalse();

    $input->url();

    expect($input->isUrl())->toBeTrue();
    expect($input->getType())->toBe('url');
});

it('can set custom `type()`', function (): void {
    $input = TextInput::make('name')
        ->type('search');

    expect($input->getType())->toBe('search');
});

it('can set `mask()`', function (): void {
    $input = TextInput::make('phone');

    expect($input->getMask())->toBeNull();

    $input->mask('(999) 999-9999');

    expect($input->getMask())->toBe('(999) 999-9999');
});

it('can set `maxValue()`', function (): void {
    $input = TextInput::make('age');

    expect($input->getMaxValue())->toBeNull();

    $input->maxValue(100);

    expect($input->getMaxValue())->toBe(100);
});

it('can set `minValue()`', function (): void {
    $input = TextInput::make('age');

    expect($input->getMinValue())->toBeNull();

    $input->minValue(0);

    expect($input->getMinValue())->toBe(0);
});

it('can set `telRegex()`', function (): void {
    $input = TextInput::make('phone')
        ->tel();

    // Default regex
    expect($input->getTelRegex())->toBe('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/');

    $input->telRegex('/^\+?[0-9]{10,15}$/');

    expect($input->getTelRegex())->toBe('/^\+?[0-9]{10,15}$/');
});

it('can set `copyable()`', function (): void {
    $input = TextInput::make('code');

    expect($input->isCopyable())->toBeFalse();

    $input->copyable();

    expect($input->isCopyable())->toBeTrue();
});

it('defaults `getType()` to `text`', function (): void {
    $input = TextInput::make('name');

    expect($input->getType())->toBe('text');
});

it('can set `integer()`', function (): void {
    $input = TextInput::make('quantity')
        ->integer();

    expect($input->isNumeric())->toBeTrue();
    expect($input->getType())->toBe('number');
});

it('can set `revealable()` on a `password()` input', function (): void {
    $input = TextInput::make('secret')
        ->password()
        ->revealable();

    expect($input->isPasswordRevealable())->toBeTrue();
});

it('can undo `email()` with `false`', function (): void {
    $input = TextInput::make('email')
        ->email()
        ->email(false);

    expect($input->isEmail())->toBeFalse();
    expect($input->getType())->toBe('text');
});

it('custom `type()` takes precedence over `email()`', function (): void {
    $input = TextInput::make('email')
        ->email()
        ->type('search');

    expect($input->getType())->toBe('search');
});

it('can set `mask()` with a `Closure`', function (): void {
    $input = TextInput::make('phone')
        ->mask(static fn (): string => '999-999-9999');

    expect($input->getMask())->toBe('999-999-9999');
});

it('can set `mask()` with `RawJs`', function (): void {
    $input = TextInput::make('phone')
        ->mask(RawJs::make('$money($input)'));

    expect($input->getMask())->toBeInstanceOf(RawJs::class);
});

it('can set `maxValue()` with a `Closure`', function (): void {
    $input = TextInput::make('score')
        ->maxValue(static fn (): int => 999);

    expect($input->getMaxValue())->toBe(999);
});

it('can set `minValue()` with a `Closure`', function (): void {
    $input = TextInput::make('score')
        ->minValue(static fn (): int => -10);

    expect($input->getMinValue())->toBe(-10);
});

it('can undo `numeric()` with `false`', function (): void {
    $input = TextInput::make('value')
        ->numeric()
        ->numeric(false);

    expect($input->isNumeric())->toBeFalse();
    expect($input->getType())->toBe('text');
});

it('can set `type()` with a `Closure`', function (): void {
    $input = TextInput::make('field')
        ->type(static fn (): string => 'search');

    expect($input->getType())->toBe('search');
});

it('can set `telRegex()` with a `Closure`', function (): void {
    $input = TextInput::make('phone')
        ->tel()
        ->telRegex(static fn (): string => '/^\d{10}$/');

    expect($input->getTelRegex())->toBe('/^\d{10}$/');
});

it('returns fluent `$this` from `currentPassword()`', function (): void {
    $input = TextInput::make('password');

    $result = $input->currentPassword();

    expect($result)->toBe($input);
});

it('returns fluent `$this` from `currentPassword()` with guard', function (): void {
    $input = TextInput::make('password');

    $result = $input->currentPassword(guard: 'admin');

    expect($result)->toBe($input);
});

class TestComponentWithNumericAndStripCharacters extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('price')
                    ->numeric()
                    ->stripCharacters(','),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

class TestComponentWithTextInputTrim extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->trim(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

describe('validation rule closures', function (): void {
    it('rejects value exceeding `maxValue()` via rule closure', function (): void {
        livewire(TestComponentWithMaxValueTextInput::class)
            ->fillForm(['quantity' => 150])
            ->call('save')
            ->assertHasFormErrors(['quantity']);
    });

    it('accepts value within `maxValue()` via rule closure', function (): void {
        livewire(TestComponentWithMaxValueTextInput::class)
            ->fillForm(['quantity' => 50])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects value below `minValue()` via rule closure', function (): void {
        livewire(TestComponentWithMinValueTextInput::class)
            ->fillForm(['quantity' => 0])
            ->call('save')
            ->assertHasFormErrors(['quantity']);
    });

    it('accepts value meeting `minValue()` via rule closure', function (): void {
        livewire(TestComponentWithMinValueTextInput::class)
            ->fillForm(['quantity' => 10])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('validates `email()` rejects invalid email', function (): void {
        livewire(TestComponentWithEmailTextInput::class)
            ->fillForm(['email' => 'not-an-email'])
            ->call('save')
            ->assertHasFormErrors(['email']);
    });

    it('validates `email()` accepts valid email', function (): void {
        livewire(TestComponentWithEmailTextInput::class)
            ->fillForm(['email' => 'test@example.com'])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('validates `url()` rejects invalid URL', function (): void {
        livewire(TestComponentWithUrlTextInput::class)
            ->fillForm(['website' => 'not-a-url'])
            ->call('save')
            ->assertHasFormErrors(['website']);
    });

    it('validates `url()` accepts valid URL', function (): void {
        livewire(TestComponentWithUrlTextInput::class)
            ->fillForm(['website' => 'https://example.com'])
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

describe('`isPasswordRevealable()` logic', function (): void {
    it('throws `LogicException` when `revealable()` is set without `password()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $input = TextInput::make('secret')->revealable(),
            ])
            ->fill();

        $input->isPasswordRevealable();
    })->throws(LogicException::class);

    it('returns `false` when `revealable()` is not set', function (): void {
        $input = TextInput::make('secret')->password();

        expect($input->isPasswordRevealable())->toBeFalse();
    });
});

class TestComponentWithMaxValueTextInput extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('quantity')
                    ->numeric()
                    ->maxValue(100),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

class TestComponentWithMinValueTextInput extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(5),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

class TestComponentWithEmailTextInput extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('email')->email(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

class TestComponentWithUrlTextInput extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('website')->url(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

describe('rendering', function (): void {
    it('can render with `password()`', function (): void {
        livewire(RenderTextInputWithPassword::class)->assertSuccessful();
    });

    it('can render with `tel()`', function (): void {
        livewire(RenderTextInputWithTel::class)->assertSuccessful();
    });

    it('can render with custom `type()`', function (): void {
        livewire(RenderTextInputWithCustomType::class)->assertSuccessful();
    });

    it('can render with `type()` set via `Closure`', function (): void {
        livewire(RenderTextInputWithClosureType::class)->assertSuccessful();
    });

    it('can render with `mask()`', function (): void {
        livewire(RenderTextInputWithMask::class)->assertSuccessful();
    });

    it('can render with `mask()` set via `Closure`', function (): void {
        livewire(RenderTextInputWithClosureMask::class)->assertSuccessful();
    });

    it('can render with `mask()` using `RawJs`', function (): void {
        livewire(RenderTextInputWithRawJsMask::class)->assertSuccessful();
    });

    it('can render with `maxValue()` set via `Closure`', function (): void {
        livewire(RenderTextInputWithClosureMaxValue::class)->assertSuccessful();
    });

    it('can render with `minValue()` set via `Closure`', function (): void {
        livewire(RenderTextInputWithClosureMinValue::class)->assertSuccessful();
    });

    it('can render with `password()` and `revealable()`', function (): void {
        livewire(RenderTextInputWithRevealable::class)->assertSuccessful();
    });

    it('can render with `integer()`', function (): void {
        livewire(RenderTextInputWithInteger::class)->assertSuccessful();
    });

    it('can render with `email(false)` undone', function (): void {
        livewire(RenderTextInputWithEmailUndone::class)->assertSuccessful();
    });

    it('can render with `numeric(false)` undone', function (): void {
        livewire(RenderTextInputWithNumericUndone::class)->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderTextInputWithPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('Enter name...');
    });
});

it('can render and type in `TextInput` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/text-input-test')
            ->assertSee('Name')
            ->assertSee('Email')
            ->assertSee('Password')
            ->type('[data-testid="text-input"] input', 'John Doe')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/text-input-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderTextInputWithPassword extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('secret')->password()])->statePath('data');
    }
}

class RenderTextInputWithTel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('phone')->tel()])->statePath('data');
    }
}

class RenderTextInputWithCustomType extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('name')->type('search')])->statePath('data');
    }
}

class RenderTextInputWithClosureType extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('field')->type(static fn (): string => 'search')])->statePath('data');
    }
}

class RenderTextInputWithMask extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('phone')->mask('(999) 999-9999')])->statePath('data');
    }
}

class RenderTextInputWithClosureMask extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('phone')->mask(static fn (): string => '999-999-9999')])->statePath('data');
    }
}

class RenderTextInputWithRawJsMask extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('phone')->mask(RawJs::make('$money($input)'))])->statePath('data');
    }
}

class RenderTextInputWithClosureMaxValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('score')->numeric()->maxValue(static fn (): int => 999)])->statePath('data');
    }
}

class RenderTextInputWithClosureMinValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('score')->numeric()->minValue(static fn (): int => -10)])->statePath('data');
    }
}

class RenderTextInputWithRevealable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('secret')->password()->revealable()])->statePath('data');
    }
}

class RenderTextInputWithInteger extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('quantity')->integer()])->statePath('data');
    }
}

class RenderTextInputWithEmailUndone extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('field')->email()->email(false)])->statePath('data');
    }
}

class RenderTextInputWithNumericUndone extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('field')->numeric()->numeric(false)])->statePath('data');
    }
}

class RenderTextInputWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TextInput::make('name')->placeholder('Enter name...')])->statePath('data');
    }
}
