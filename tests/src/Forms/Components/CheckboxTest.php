<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

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
