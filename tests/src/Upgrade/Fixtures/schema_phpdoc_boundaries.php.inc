<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaPhpDocBoundaries;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Exports\Exporter;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

abstract class DualExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return ['old'];
    }

    public static function getOptionsSchemaComponents(): array
    {
        return ['new'];
    }
}

class CustomSchema extends Schema
{
    public function getComponent(string | Closure $findComponentUsing, bool $withActions = true, bool $withHidden = false, bool $isAbsoluteKey = false, array $skipComponentsChildContainersWhileSearching = []): Component | Action | ActionGroup | null
    {
        return null;
    }
}

class CustomAction extends Action
{
    public function fillForm(array | Closure | null $customData): static
    {
        return $this;
    }
}

/**
 * @param Exporter | DualExporter $exporter
 * @param Schema | CustomSchema $schema
 * @param Action | CustomAction $action
 */
function inspect($exporter, $schema, $action): void
{
    $exporter::getOptionsFormComponents();
    $schema->getComponent('name', skipComponentsChildContainersWhileSearching: []);
    $action->fillForm(customData: []);
}
