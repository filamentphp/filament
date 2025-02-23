<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('has a form with the default name \'form\'', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormExists();
});

it('can have forms with non-default names', function (): void {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormExists('fooForm')
        ->assertFormExists('barForm');
});

it('has fields', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('nested.input')
        ->assertFormFieldExists('disabled', function (TextInput $field): bool {
            return $field->isDisabled();
        });
});

it('does not have fields', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldDoesNotExist('not-such-field');
});

it('has fields on multiple forms', function (): void {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldExists('title', 'fooForm')
        ->assertFormFieldExists('title', 'barForm')
        ->assertFormFieldExists('disabled', 'barForm', function (TextInput $field): bool {
            return $field->isDisabled();
        });
});

it('can fill fields on multiple forms', function (): void {
    livewire(TestComponentWithMultipleForms::class)
        ->fillForm(['title' => 'value'], 'fooForm')
        ->assertFormSet(['title' => 'value'], 'fooForm');
});

it('can have disabled fields', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldDisabled('disabled');
});

it('can have disabled fields on multiple forms', function (): void {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldDisabled('disabled', 'fooForm')
        ->assertFormFieldDisabled('disabled', 'barForm');
});

it('can have enabled fields', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldEnabled('enabled');
});

it('can have enabled fields on multiple forms', function (): void {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldEnabled('enabled', 'fooForm')
        ->assertFormFieldEnabled('enabled', 'barForm');
});

it('can have hidden fields', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldHidden('hidden');
});

it('can have hidden fields on multiple forms', function (): void {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldHidden('hidden', 'fooForm')
        ->assertFormFieldHidden('hidden', 'barForm');
});

it('can have visible fields', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormFieldVisible('visible');
});

it('can have visible fields on multiple forms', function (): void {
    livewire(TestComponentWithMultipleForms::class)
        ->assertFormFieldVisible('visible', 'fooForm')
        ->assertFormFieldVisible('visible', 'barForm');
});

it('has layout components', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormComponentExists('section')
        ->assertFormComponentExists('section.nested.section')
        ->assertFormComponentExists('section.nested.section', function (Section $section): bool {
            return $section->getHeading() === 'I am nested';
        });
});

it('does not have layout components', function (): void {
    livewire(TestComponentWithForm::class)
        ->assertFormComponentDoesNotExist('no-such-section');
});

it('can go to next wizard step on multiple forms', function (): void {
    livewire(TestComponentWithMultipleWizardForms::class)
        ->assertHasNoFormErrors(formName: 'fooForm')
        ->assertHasNoFormErrors(formName: 'barForm')

        ->assertWizardStepExists(2, 'fooForm')
        ->goToWizardStep(2, formName: 'fooForm')
        ->assertHasFormErrors(['title'], 'fooForm')
        ->assertHasNoFormErrors(['title'], 'barForm');
});

class TestComponentWithForm extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
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
            ])
            ->statePath('data');
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

    public function fooForm(Schema $form): Schema
    {
        return $form
            ->components($this->getSchemaForForms())
            ->statePath('data');
    }

    public function barForm(Schema $form): Schema
    {
        return $form
            ->components($this->getSchemaForForms())
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
}

class TestComponentWithMultipleWizardForms extends Livewire
{
    public $fooData;

    public $barData;

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

    public function fooForm(Schema $form): Schema
    {
        return $form
            ->components($this->getSchemaForForms())
            ->statePath('fooData');
    }

    public function barForm(Schema $form): Schema
    {
        return $form
            ->components($this->getSchemaForForms())
            ->statePath('barData');
    }

    protected function getSchemaForForms(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make('step 1')->schema([
                    TextInput::make('title')->required(),
                ]),
                Wizard\Step::make('step 2')->schema([
                    TextInput::make('content')->required(),
                ]),
            ]),
        ];
    }
}
