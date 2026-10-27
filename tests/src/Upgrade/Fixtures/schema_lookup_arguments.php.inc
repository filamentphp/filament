<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaLookupArguments;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component as LivewireComponent;

class Form extends LivewireComponent implements HasSchemas
{
    use InteractsWithSchemas;

    public function inspect(): void
    {
        $this->getSchemaComponent('form.name', skipComponentsChildContainersWhileSearching: []);
    }
}

class CustomSchema extends Schema
{
    public function getComponent(string | Closure $findComponentUsing, bool $withActions = true, bool $withHidden = false, bool $isAbsoluteKey = false, array $skipComponentsChildContainersWhileSearching = []): Component | Action | ActionGroup | null
    {
        return null;
    }

    public function findField(): mixed
    {
        return parent::getComponent('name', skipComponentsChildContainersWhileSearching: []);
    }
}

function inspect(Schema $schema, ?Schema $nullable, HasSchemas $livewire, CustomSchema $custom, mixed $unknown, array $arguments): void
{
    $schema->getComponent('name', skipComponentsChildContainersWhileSearching: [], withHidden: true);
    $nullable?->getComponentByStatePath('name', skipComponentsChildContainersWhileSearching: []);
    $livewire->getSchemaComponent('form.name', skipComponentsChildContainersWhileSearching: []);
    $schema->getComponent('name', true, false, false, []);
    $schema->getComponent(...$arguments);
    $custom->getComponent('name', skipComponentsChildContainersWhileSearching: []);
    $unknown->getComponent('name', skipComponentsChildContainersWhileSearching: []);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaLookupArguments;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component as LivewireComponent;

class Form extends LivewireComponent implements HasSchemas
{
    use InteractsWithSchemas;

    public function inspect(): void
    {
        $this->getSchemaComponent('form.name', skipComponentsChildSchemasWhileSearching: []);
    }
}

class CustomSchema extends Schema
{
    public function getComponent(string | Closure $findComponentUsing, bool $withActions = true, bool $withHidden = false, bool $isAbsoluteKey = false, array $skipComponentsChildContainersWhileSearching = []): Component | Action | ActionGroup | null
    {
        return null;
    }

    public function findField(): mixed
    {
        return parent::getComponent('name', skipComponentsChildSchemasWhileSearching: []);
    }
}

function inspect(Schema $schema, ?Schema $nullable, HasSchemas $livewire, CustomSchema $custom, mixed $unknown, array $arguments): void
{
    $schema->getComponent('name', skipComponentsChildSchemasWhileSearching: [], withHidden: true);
    $nullable?->getComponentByStatePath('name', skipComponentsChildSchemasWhileSearching: []);
    $livewire->getSchemaComponent('form.name', skipComponentsChildSchemasWhileSearching: []);
    $schema->getComponent('name', true, false, false, []);
    $schema->getComponent(...$arguments);
    $custom->getComponent('name', skipComponentsChildContainersWhileSearching: []);
    $unknown->getComponent('name', skipComponentsChildContainersWhileSearching: []);
}
