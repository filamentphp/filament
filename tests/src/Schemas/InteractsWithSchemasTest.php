<?php

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can resolve schema using the primary method name', function (): void {
    $component = new class extends Livewire
    {
        public function test(Schema $schema): Schema
        {
            return $schema;
        }
    };

    expect($component->getSchema('test'))->toBeInstanceOf(Schema::class);
});

it('can resolve schema using the fallback method name', function (): void {
    $component = new class extends Livewire
    {
        public function testSchema(Schema $schema): Schema
        {
            return $schema;
        }
    };

    expect($component->getSchema('test'))->toBeInstanceOf(Schema::class);
});

it('can resolve schema using fallback method name without parameters', function (): void {
    $component = new class extends Livewire
    {
        public function testSchema(): Schema
        {
            return Schema::make($this);
        }
    };

    expect($component->getSchema('test'))->toBeInstanceOf(Schema::class);
});

it('cannot validate from a client-side call', function (string $method): void {
    // `validate()` and `validateOnly()` are not part of a component's client-callable
    // surface, but Livewire only withholds methods declared on its own base component,
    // and both are redeclared here. Running them from the client would execute every
    // schema's validation rules outside the flow that is supposed to gate them.
    livewire(TestComponentWithValidation::class)
        ->fillForm(['title' => ''])
        ->call($method, ...(($method === 'validateOnly') ? ['data.title'] : []))
        ->assertForbidden();
})->with(['validateOnly', 'validate']);

it('cannot validate from a client-side call alongside another method', function (): void {
    $livewire = livewire(TestComponentWithValidation::class)
        ->fillForm(['title' => '']);

    $lastState = (new ReflectionProperty($livewire, 'lastState'))->getValue($livewire);

    // Both calls are made in one request, so that an implementation which only
    // guarded the first call would be caught.
    $this->withHeaders(['X-Livewire' => true])
        ->post(app('livewire')->getUpdateUri(), [
            'components' => [
                [
                    'snapshot' => json_encode($lastState->getSnapshot()),
                    'updates' => [],
                    'calls' => [
                        ['method' => 'validateOnly', 'params' => ['data.title'], 'path' => ''],
                        ['method' => 'submit', 'params' => [], 'path' => ''],
                    ],
                ],
            ],
        ])
        ->assertForbidden();
});

it('can validate from the component itself', function (): void {
    livewire(TestComponentWithValidation::class)
        ->fillForm(['title' => ''])
        ->call('submit')
        ->assertHasFormErrors(['title' => ['required']]);
});

it('can remove items from a list field in a mounted action form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->mountAction('roles')
        ->assertSchemaStateSet(['roles' => ['viewer', 'creator']])
        ->fillForm(['roles' => ['viewer']])
        ->assertSchemaStateSet(['roles' => ['viewer']]);
});

it('can remove items from a list field in a page form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->assertFormSet(['roles' => ['viewer', 'creator']])
        ->fillForm(['roles' => ['viewer']])
        ->assertFormSet(['roles' => ['viewer']]);
});

it('can clear a list field in a mounted action form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->mountAction('roles')
        ->fillForm(['roles' => []])
        ->assertSchemaStateSet(['roles' => []]);
});

it('can add items to a list field in a mounted action form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->mountAction('roles')
        ->fillForm(['roles' => ['viewer', 'creator', 'editor']])
        ->assertSchemaStateSet(['roles' => ['viewer', 'creator', 'editor']]);
});

class TestComponentWithListFields extends Livewire
{
    public function mount(): void
    {
        $this->form->fill(['roles' => ['viewer', 'creator']]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                CheckboxList::make('roles')
                    ->options([
                        'viewer' => 'Viewer',
                        'creator' => 'Creator',
                        'editor' => 'Editor',
                    ]),
            ])
            ->statePath('data');
    }

    public function rolesAction(): Action
    {
        return Action::make('roles')
            ->fillForm(['roles' => ['viewer', 'creator']])
            ->schema([
                CheckboxList::make('roles')
                    ->options([
                        'viewer' => 'Viewer',
                        'creator' => 'Creator',
                        'editor' => 'Editor',
                    ]),
            ])
            ->action(static function (): void {});
    }
}

class TestComponentWithValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('title')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $this->form->validate();
    }
}
