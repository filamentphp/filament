<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render', function (): void {
    livewire(TestComponentWithCheckbox::class)
        ->assertSuccessful();
});

it('can set and get boolean state', function (): void {
    livewire(TestComponentWithCheckbox::class)
        ->fillForm(['accepted' => true])
        ->assertSchemaStateSet(['accepted' => true]);
});

it('can render inline', function (): void {
    livewire(TestComponentWithInlineCheckbox::class)
        ->assertSuccessful();
});

it('can validate with `accepted()` rule', function (): void {
    livewire(TestComponentWithAcceptedCheckbox::class)
        ->fillForm(['terms' => false])
        ->call('save')
        ->assertHasFormErrors(['terms' => ['accepted']]);
});

it('passes validation when `accepted()` checkbox is checked', function (): void {
    livewire(TestComponentWithAcceptedCheckbox::class)
        ->fillForm(['terms' => true])
        ->call('save')
        ->assertHasNoFormErrors();
});

class TestComponentWithCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithInlineCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')->inline(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithDeclinedCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('marketing')->declined(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithAcceptedCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('terms')->accepted(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

describe('inline', function (): void {
    it('defaults `isInline()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $checkbox = Checkbox::make('accepted'),
            ])
            ->fill();

        expect($checkbox->isInline())->toBeTrue();
    });

    it('can set `inline()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $checkbox = Checkbox::make('accepted')->inline(false),
            ])
            ->fill();

        expect($checkbox->isInline())->toBeFalse();
    });

    it('can render with `inline(false)`', function (): void {
        livewire(TestComponentWithNonInlineCheckbox::class)
            ->assertSuccessful();
    });

    it('can set `inline()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $checkbox = Checkbox::make('accepted')
                    ->inline(static fn (): bool => false),
            ])
            ->fill();

        expect($checkbox->isInline())->toBeFalse();
    });

    it('can render with `inline()` set via `Closure`', function (): void {
        livewire(TestComponentWithClosureInlineCheckbox::class)
            ->assertSuccessful();
    });

    it('returns `false` for `isInline()` when `inlineLabel()` is set', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $checkbox = Checkbox::make('accepted')
                    ->inline()
                    ->inlineLabel(),
            ])
            ->fill();

        expect($checkbox->isInline())->toBeFalse();
    });

    it('can render with `inline()` and `inlineLabel()`', function (): void {
        livewire(TestComponentWithInlineLabelCheckbox::class)
            ->assertSuccessful();
    });
});

describe('`declined()` validation', function (): void {
    it('fails validation when `declined()` checkbox is checked', function (): void {
        livewire(TestComponentWithDeclinedCheckbox::class)
            ->fillForm(['marketing' => true])
            ->call('save')
            ->assertHasFormErrors(['marketing' => ['declined']]);
    });

    it('passes validation when `declined()` checkbox is unchecked', function (): void {
        livewire(TestComponentWithDeclinedCheckbox::class)
            ->fillForm(['marketing' => false])
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

describe('extra input attributes', function (): void {
    it('can set `extraInputAttributes()`', function (): void {
        $checkbox = Checkbox::make('accepted')
            ->extraInputAttributes(['data-test' => 'foo']);

        expect($checkbox->getExtraInputAttributes())->toBe(['data-test' => 'foo']);
    });

    it('can render with `extraInputAttributes()`', function (): void {
        livewire(TestComponentWithExtraInputAttributesCheckbox::class)
            ->assertSuccessful();
    });

    it('can merge `extraInputAttributes()`', function (): void {
        $checkbox = Checkbox::make('accepted')
            ->extraInputAttributes(['data-a' => '1'])
            ->extraInputAttributes(['data-b' => '2'], merge: true);

        $attributes = $checkbox->getExtraInputAttributes();

        expect($attributes)->toHaveKey('data-a', '1');
        expect($attributes)->toHaveKey('data-b', '2');
    });

    it('can render with merged `extraInputAttributes()`', function (): void {
        livewire(TestComponentWithMergedExtraInputAttributesCheckbox::class)
            ->assertSuccessful();
    });

    it('replaces `extraInputAttributes()` without merge', function (): void {
        $checkbox = Checkbox::make('accepted')
            ->extraInputAttributes(['data-a' => '1'])
            ->extraInputAttributes(['data-b' => '2']);

        $attributes = $checkbox->getExtraInputAttributes();

        expect($attributes)->not->toHaveKey('data-a');
        expect($attributes)->toHaveKey('data-b', '2');
    });

    it('can set `extraInputAttributes()` with a `Closure`', function (): void {
        $checkbox = Checkbox::make('accepted')
            ->extraInputAttributes(static fn (): array => ['data-dynamic' => 'value']);

        expect($checkbox->getExtraInputAttributes())->toBe(['data-dynamic' => 'value']);
    });

    it('can render with `extraInputAttributes()` set via `Closure`', function (): void {
        livewire(TestComponentWithClosureExtraInputAttributesCheckbox::class)
            ->assertSuccessful();
    });

    it('returns an `ComponentAttributeBag` from `getExtraInputAttributeBag()`', function (): void {
        $checkbox = Checkbox::make('accepted')
            ->extraInputAttributes(['data-test' => 'bar']);

        $bag = $checkbox->getExtraInputAttributeBag();

        expect($bag)->toBeInstanceOf(ComponentAttributeBag::class);
        expect($bag->get('data-test'))->toBe('bar');
    });
});

it('defaults state to `false`', function (): void {
    $checkbox = Checkbox::make('accepted');

    expect($checkbox->getDefaultState())->toBeFalse();
});

it('can render with a custom `label()`', function (): void {
    livewire(TestComponentWithLabelledCheckbox::class)
        ->assertSuccessful()
        ->assertSeeHtml('I Accept the Terms');
});

it('can render `Checkbox` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/checkbox-test')
            ->assertSee('Test Checkbox')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/checkbox-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class TestComponentWithNonInlineCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')->inline(false),
            ])
            ->statePath('data');
    }
}

class TestComponentWithClosureInlineCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')
                    ->inline(static fn (): bool => false),
            ])
            ->statePath('data');
    }
}

class TestComponentWithInlineLabelCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')
                    ->inline()
                    ->inlineLabel(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithExtraInputAttributesCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')
                    ->extraInputAttributes(['data-test' => 'foo']),
            ])
            ->statePath('data');
    }
}

class TestComponentWithMergedExtraInputAttributesCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')
                    ->extraInputAttributes(['data-a' => '1'])
                    ->extraInputAttributes(['data-b' => '2'], merge: true),
            ])
            ->statePath('data');
    }
}

class TestComponentWithClosureExtraInputAttributesCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')
                    ->extraInputAttributes(static fn (): array => ['data-dynamic' => 'value']),
            ])
            ->statePath('data');
    }
}

class TestComponentWithLabelledCheckbox extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('accepted')
                    ->label('I Accept the Terms'),
            ])
            ->statePath('data');
    }
}
