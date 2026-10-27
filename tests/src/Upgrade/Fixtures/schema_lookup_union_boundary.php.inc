<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaLookupUnionBoundary;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class CustomSchema extends Schema
{
    public function getComponent(string | Closure $findComponentUsing, bool $withActions = true, bool $withHidden = false, bool $isAbsoluteKey = false, array $skipComponentsChildContainersWhileSearching = []): Component | Action | ActionGroup | null
    {
        return null;
    }
}

function inspect(Schema | CustomSchema $schema): void
{
    $schema->getComponent('name', skipComponentsChildContainersWhileSearching: []);
}
