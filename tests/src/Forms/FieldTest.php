<?php

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

uses(TestCase::class);

it('sets its state path from its name', function (): void {
    $field = (new Field($name = Str::random()))
        ->container(Schema::make(Livewire::make()));

    expect($field)
        ->getStatePath()->toBe($name);
});

it('sets its fallback label from its name', function (): void {
    $field = (new Field($name = Str::random()))
        ->container(Schema::make(Livewire::make()));

    expect($field)
        ->getLabel()->toBe(
            (string) Str::of($name)
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst(),
        );
});

it('can be instantiated with a default name', function (): void {
    $field = IdField::make();

    expect($field->getName())
        ->toBe('id');
});

it('can ignore the default name if another is specified', function (): void {
    $field = IdField::make('identifier');

    expect($field->getName())
        ->toBe('identifier');
});

it('can use a custom view via `fieldWrapperView()`', function (): void {
    $html = Schema::make(Livewire::make())
        ->components([
            TextInput::make('name')
                ->fieldWrapperView('custom-field-wrapper'),
        ])
        ->toHtml();

    expect($html)
        ->toContain('custom-field-wrapper-view')
        ->toContain('data-label="Name"')
        ->toContain('<input');
});

it('can use a Blade component alias via `fieldWrapperView()`', function (): void {
    // The `$errors` view error bag is usually shared by the `ShareErrorsFromSession` middleware.
    view()->share('errors', new ViewErrorBag);

    $html = Schema::make(Livewire::make())
        ->components([
            TextInput::make('name')
                ->fieldWrapperView('test-plugin-wrapper'),
        ])
        ->toHtml();

    expect($html)
        ->toContain('field-wrapper-blade-component')
        ->toContain('fi-fo-field')
        ->toContain('Name')
        ->toContain('<input');
});

describe('validation messages for nested recursive rules', function (): void {
    $shareErrors = function (array $messages): void {
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag($messages)));
    };

    it('renders a single per-element message', function () use ($shareErrors): void {
        $shareErrors(['data.choices.0' => ['The first choice is invalid.']]);

        $html = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                CheckboxList::make('choices')
                    ->options(['alpha' => 'Alpha'])
                    ->showAllValidationMessages(),
            ])
            ->toHtml();

        expect($html)
            ->toContain('The first choice is invalid.');
    });

    it('renders every per-element message when more than one index fails', function () use ($shareErrors): void {
        $shareErrors([
            'data.choices.0' => ['The first choice is invalid.'],
            'data.choices.1' => ['The second choice is invalid.'],
        ]);

        $html = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                CheckboxList::make('choices')
                    ->options(['alpha' => 'Alpha'])
                    ->showAllValidationMessages(),
            ])
            ->toHtml();

        expect($html)
            ->toContain('The first choice is invalid.')
            ->toContain('The second choice is invalid.');
    });

    it('renders a per-element message through a Blade field wrapper', function () use ($shareErrors): void {
        $shareErrors(['data.choices.0' => ['The first choice is invalid.']]);

        $html = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                CheckboxList::make('choices')
                    ->options(['alpha' => 'Alpha'])
                    ->showAllValidationMessages()
                    ->fieldWrapperView('test-plugin-wrapper'),
            ])
            ->toHtml();

        expect($html)
            ->toContain('The first choice is invalid.');
    });
});

class IdField extends TextInput
{
    public static function getDefaultName(): ?string
    {
        return 'id';
    }
}
