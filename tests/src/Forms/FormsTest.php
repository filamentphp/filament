<?php

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('has a form with the default name \'form\'', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormExists();
});

it('can have forms with non-default names', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormExists('fooForm')
        ->assertFormExists('barForm');
});

it('has fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('nested.input')
        ->assertFormFieldExists('disabled', function (TextInput $field): bool {
            return $field->isDisabled();
        });
});

it('does not have fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldDoesNotExist('not-such-field');
});

it('has fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldExists('title', 'fooForm')
        ->assertFormFieldExists('title', 'barForm')
        ->assertFormFieldExists('disabled', 'barForm', function (TextInput $field): bool {
            return $field->isDisabled();
        });
});

it('can fill fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->fillForm(['title' => 'value'], 'fooForm')
        ->assertFormSet(['title' => 'value'], 'fooForm');
});

it('can have disabled fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsDisabled('disabled');
});

it('can have disabled fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsDisabled('disabled', 'fooForm')
        ->assertFormFieldIsDisabled('disabled', 'barForm');
});

it('can have enabled fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsEnabled('enabled');
});

it('can have enabled fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsEnabled('enabled', 'fooForm')
        ->assertFormFieldIsEnabled('enabled', 'barForm');
});

it('can have hidden fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsHidden('hidden');
});

it('can have hidden fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsHidden('hidden', 'fooForm')
        ->assertFormFieldIsHidden('hidden', 'barForm');
});

it('can have visible fields', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldIsVisible('visible');
});

it('can have visible fields on multiple forms', function () {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldIsVisible('visible', 'fooForm')
        ->assertFormFieldIsVisible('visible', 'barForm');
});

it('has layout components', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormComponentExists('section')
        ->assertFormComponentExists('disabled-section', function (Section $section): bool {
            return $section->isDisabled();
        })
        ->assertFormComponentExists('nested.section')
        ->assertFormComponentExists('nested.section', function (Section $section): bool {
            return $section->getHeading() === 'I am nested';
        });
});

it('does not have layout components', function () {
    livewire(TestComponentWithForm::class)
        ->assertFormComponentDoesNotExist('no-such-section');
});

class TestComponentWithForm extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),

                TextInput::make('nested.input'),

                TextInput::make('disabled')
                    ->disabled(),

                TextInput::make('enabled'),

                TextInput::make('hidden')
                    ->hidden(),

                TextInput::make('visible'),

                Section::make()
                    ->key('section')
                    ->schema([
                        Section::make('I am nested')
                            ->key('nested.section'),
                    ]),

                Section::make()
                    ->key('disabled-section')
                    ->disabled(),
            ]);
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class TestComponentWithMultipleForms extends Livewire
{
    public function mount(): void
    {
        $this->fooForm->fill();
        $this->barForm->fill();
    }

    protected function getForms(): array
    {
        return [
            'fooForm',
            'barForm',
        ];
    }

    public function fooForm(Form $form): Form
    {
        return $form
            ->schema($this->getSchemaForForms())
            ->statePath('data');
    }

    public function barForm(Form $form): Form
    {
        return $form
            ->schema($this->getSchemaForForms())
            ->statePath('data');
    }

    protected function getSchemaForForms(): array
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
