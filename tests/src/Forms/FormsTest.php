<?php

use Filament\Forms\Components\TextInput;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('has a form with the default name \'form\'', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormExists();
})->only();

it('can have forms with non-default names', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormExists('fooForm')
        ->assertFormExists('barForm');
})->only();

it('has fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldExists('title');
})->only();

it('has fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldExists('title', 'fooForm')
        ->assertFormFieldExists('title', 'barForm');
})->only();

it('can have disabled fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsDisabled('disabled');
})->only();

it('can have disabled fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsDisabled('disabled', 'fooForm')
        ->assertFormFieldIsDisabled('disabled', 'barForm');
})->only();

it('can have enabled fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsEnabled('enabled');
})->only();

it('can have enabled fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsEnabled('enabled', 'fooForm')
        ->assertFormFieldIsEnabled('enabled', 'barForm');
})->only();

it('can have hidden fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsHidden('hidden');
})->only();

it('can have hidden fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsHidden('hidden', 'fooForm')
        ->assertFormFieldIsHidden('hidden', 'barForm');
})->only();

it('can have visible fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsVisible('visible');
})->only();

it('can have visible fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsVisible('visible', 'fooForm')
        ->assertFormFieldIsVisible('visible', 'barForm');
})->only();

class TestComponentWithForm extends Livewire
{
    public function getFormSchema(): array
    {
        return [
            TextInput::make('title'),

            TextInput::make('disabled')
                ->disabled(),

            TextInput::make('enabled'),

            TextInput::make('hidden')
                ->hidden(),

            TextInput::make('visible'),
        ];
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class TestComponentWithMultipleForms extends Livewire
{
    protected function getForms(): array
    {
        return [
            'fooForm' => $this->makeForm()
                ->schema($this->getSchemaForForms()),

            'barForm' => $this->makeForm()
                ->schema($this->getSchemaForForms()),
        ];
    }

    private function getSchemaForForms(): array
    {
        return [
            TextInput::make('title'),

            TextInput::make('disabled')
                ->disabled(),

            TextInput::make('enabled'),

            TextInput::make('hidden')
                ->hidden(),

            TextInput::make('visible'),
        ];
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
