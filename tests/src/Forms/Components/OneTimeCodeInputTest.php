<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

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
