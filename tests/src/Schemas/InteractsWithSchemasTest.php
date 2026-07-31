<?php

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
        ->postJson(app('livewire')->getUpdateUri(), [
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
