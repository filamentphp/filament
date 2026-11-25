<?php

namespace Filament\Tests\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Livewire\Attributes\Computed;
use Livewire\Exceptions\PropertyNotFoundException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('does not discover schemas through request lookups', function (string $method, array $arguments): void {
    $component = livewire(SchemaDiscoveryComponent::class)
        ->call($method, ...$arguments)
        ->assertSet('discoveredSchemaNames', [])
        ->assertSet('loadedLazySchemas', [])
        ->assertSet('mountedActions', [])
        ->assertSet('data', null);

    expect($component->instance()->getSchemaComponent('restricted.probe'))->toBeNull();

    $component->call('_startUpload', 'data.document', [['name' => 'document.txt', 'size' => 1024, 'type' => 'text/plain']], false)
        ->assertForbidden();
})->with([
    'component method' => ['callSchemaComponentMethod', ['restricted.probe', 'touchState']],
    'partial render' => ['partiallyRenderSchemaComponent', ['restricted.probe']],
    'deferred schema' => ['loadLazySchema', ['restricted']],
    'schema action' => ['mountAction', ['touchState', [], ['schemaComponent' => 'restricted']]],
]);

it('does not discover schemas or actions through upload property lookup', function (string $name): void {
    $component = livewire(SchemaDiscoveryComponent::class);

    expect(fn () => $component->call('_removeUpload', $name, 'missing'))
        ->toThrow(PropertyNotFoundException::class);

    expect($component->instance()->discoveredSchemaNames)->toBe([]);
})->with(['restricted', 'restricted.document', 'unregisteredAction', 'unknown']);

it('preserves cached action ownership during property lookup', function (): void {
    $component = app(SchemaDiscoveryComponent::class);
    $parent = Action::make('parent');
    $action = $component->cacheAction(Action::make('cached')->mountedParentAction($parent)->nestingIndex(3));

    expect($component->getPropertyValue('cached'))->toBe($action)
        ->and($action->evaluate(static fn (?Action $parentAction): ?Action => $parentAction))->toBe($parent)
        ->and($action->getNestingIndex())->toBe(3);
});

it('preserves registered schemas and trusted filling across requests', function (string $componentClass, array $discoveredSchemaNames): void {
    $component = livewire($componentClass)
        ->assertSet('data.name', 'Initial')
        ->assertSet('discoveredSchemaNames', $discoveredSchemaNames)
        ->call('callSchemaComponentMethod', 'restricted.probe', 'touchState')
        ->assertSet('data.touched', true)
        ->call('partiallyRenderSchemaComponent', 'restricted.probe')
        ->call('loadLazySchema', 'restricted')
        ->assertSet('loadedLazySchemas.restricted', true)
        ->call('mountAction', 'touchState', [], ['schemaComponent' => 'restricted'])
        ->assertSet('data.actionTouched', true);

    expect($component->instance()->getSchemaComponent('restricted.probe'))->toBeInstanceOf(SchemaDiscoveryProbe::class)
        ->and($component->instance()->getPropertyValue('restricted'))->toBeInstanceOf(Schema::class)
        ->and($component->instance()->getSchemaComponent('restricted.nested.child'))->toBeInstanceOf(TextInput::class);
})->with([
    'discovered during mount' => [RegisteredSchemaDiscoveryComponent::class, ['restricted']],
    'explicitly cached' => [CachedSchemaDiscoveryComponent::class, []],
]);

it('resolves components in mounted-action schemas', function (): void {
    $component = livewire(RegisteredSchemaDiscoveryComponent::class)
        ->call('mountAction', 'configure', [], ['schemaComponent' => 'restricted'])
        ->call('callSchemaComponentMethod', 'mountedActionSchema0.probe', 'touchState')
        ->assertSet('data.touched', true)
        ->assertSet('mountedActions.0.name', 'configure');

    expect($component->instance()->getSchemaComponent('mountedActionSchema0.document'))->toBeInstanceOf(FileUpload::class);
});

it('preserves native and computed property lookup semantics', function (): void {
    $component = livewire(SchemaDiscoveryComponent::class)->instance();

    expect($component->getPropertyValue(name: 'data'))->toBeNull()
        ->and($component->getPropertyValue('summary.name'))->toBe('Computed')
        ->and(fn () => $component->getPropertyValue('missing'))->toThrow(PropertyNotFoundException::class)
        ->and($component->discoveredSchemaNames)->toBe([]);
});

class SchemaDiscoveryComponent extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function mount(): void {}

    public function restricted(Schema $schema): Schema
    {
        return $schema->statePath('data')->lazyLoaded()->components([
            TextInput::make('name')->default('Initial'),
            FileUpload::make('document'),
            SchemaDiscoveryProbe::make()->key('probe'),
            Group::make([TextInput::make('child')])->key('nested'),
            Action::make('touchState')->action(function (): void {
                $this->data['actionTouched'] = true;
            }),
            Action::make('configure')->schema([
                SchemaDiscoveryProbe::make()->key('probe'),
                FileUpload::make('document'),
            ]),
        ]);
    }

    #[Computed]
    public function summary(): array
    {
        return ['name' => 'Computed'];
    }

    public function unregisteredAction(): Action
    {
        return Action::make('unregisteredAction');
    }
}

class RegisteredSchemaDiscoveryComponent extends SchemaDiscoveryComponent
{
    public function mount(): void
    {
        $this->restricted->fill();
    }
}

class CachedSchemaDiscoveryComponent extends RegisteredSchemaDiscoveryComponent
{
    public function boot(): void
    {
        $this->cacheSchema('restricted', $this->restricted(Schema::make($this)));
    }
}

class SchemaDiscoveryProbe extends Group
{
    #[Exposed]
    public function touchState(): void
    {
        $this->getLivewire()->data['touched'] = true;
    }
}
