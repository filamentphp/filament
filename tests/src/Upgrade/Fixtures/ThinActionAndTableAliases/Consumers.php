<?php

namespace Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use RuntimeException;
use Throwable;

trait MixedConsumers
{
    public function sharedReceiver(): static
    {
        return $this;
    }

    public function update(): array
    {
        $component = $this;

        return [
            $this->updatedToggledTableColumns(effect('shared-extra')),
            ($this->updatedToggledTableColumns(...))(effect('shared-callable')),
            $component->updatedToggledTableColumns(),
            $component->sharedReceiver()->updatedToggledTableColumns(),
        ];
    }

    public function nullableCalls(?object $owner, ?object $holder): array
    {
        $results = [
            $owner?->updatedToggledTableColumns(effect('shared-nullable')),
            $holder?->owner->updatedToggledTableColumns(effect('shared-holder')),
            $owner?->updatedToggledTableColumns(effect('shared-downstream'))->missing(),
            $owner?->updatedToggledTableColumns()['missing'] ?? 'fallback',
            isset($owner?->updatedToggledTableColumns()['missing']),
            empty($owner?->updatedToggledTableColumns()['missing']),
        ];

        return $results;
    }

    public function nullableControl(object $owner): array
    {
        try {
            $owner?->updatedToggledTableColumns(effect('shared-existing'))->missing();
        } catch (Throwable $exception) {
            return [$exception::class, $owner?->updatedToggledTableColumns(effect('shared-control'))];
        }

        throw new RuntimeException('An existing receiver returning `null` must not short-circuit');
    }
}

class SupportedMixedConsumer extends Owner
{
    use MixedConsumers;
}

class UnrelatedMixedConsumer
{
    use MixedConsumers;

    public function updatedToggledTableColumns(): string
    {
        return 'unrelated';
    }
}

class MagicMixedConsumer
{
    use MixedConsumers;

    public function __call(string $method, array $arguments): string
    {
        Owner::$events[] = [$method, $arguments];

        return 'magic';
    }
}

trait ReadsAliases
{
    public function inspect(?Action $action): array
    {
        $this->getMountedActionForm();
        $this->getMountedActionForm(mountedAction: $action, actionNestingIndex: 2);
        $this->getMountedActionForm(...['mountedAction' => $action]);
        $this->getToggledTableColumnsSessionKey(effect('session-extra'));
        $this->updatedTableGroupColumn(effect('group-extra'));
        self::updatedToggledTableColumns(effect('relative-extra'));

        return [
            $this->getMountedActionForm(...),
            $this->getToggledTableColumnsSessionKey(...),
            $this->updatedTableGroupColumn(...),
        ];
    }
}

class InheritedOwner extends Owner
{
    use ReadsAliases;

    public function inspectOther(Owner $owner): void
    {
        $owner->getMountedActionForm(actionNestingIndex: 4);
        $owner->getToggledTableColumnsSessionKey();
    }
}

trait CustomAliases
{
    public function parentCallables(): array
    {
        return [parent::mountedActionHasForm(...), parent::getMountedActionForm(...), parent::updatedToggledTableColumns(...)];
    }

    #[Unexposed]
    public function updatedToggledTableColumns(): void
    {
        static::$events[] = 'custom-columns';
        parent::updatedToggledTableColumns(effect('parent-extra'));
    }

    #[Unexposed]
    public function mountedActionHasForm(?Action $mountedAction = null): bool
    {
        static::$events[] = 'custom-has';

        return parent::mountedActionHasForm(mountedAction: $mountedAction);
    }

    protected function getMountedActionForm(?int $actionNestingIndex = null, ?Action $mountedAction = null): ?Schema
    {
        static::$events[] = 'custom-schema';

        return parent::getMountedActionForm($actionNestingIndex, $mountedAction);
    }

    protected function getToggledTableColumnsSessionKey(): string
    {
        return 'custom-' . parent::getToggledTableColumnsSessionKey();
    }

    #[Unexposed]
    public function updatedTableGroupColumn(): void
    {
        static::$events[] = 'custom-group';
        parent::updatedTableGroupColumn();
    }
}

class CustomOwner extends Owner
{
    use CustomAliases;

    public function inspect(): array
    {
        return [$this->getMountedActionForm(), $this->getToggledTableColumnsSessionKey()];
    }
}

class CustomDescendant extends CustomOwner
{
    #[Unexposed]
    public function mountedActionHasSchema(?Action $mountedAction = null, string $additional = 'default'): bool
    {
        static::$events[] = 'descendant-has';

        return parent::mountedActionHasSchema($mountedAction, $additional);
    }

    #[Exposed]
    public function applyTableColumnManager(?array $state = null, bool $wasReordered = false): void
    {
        static::$events[] = 'descendant-columns';
        parent::applyTableColumnManager($state, $wasReordered);
    }
}

class Unrelated
{
    public function updatedToggledTableColumns(): string
    {
        return 'unrelated';
    }
}

function effect(string $event): array
{
    Owner::$events[] = $event;

    return ['ignored'];
}

function computed(Owner $owner): Owner
{
    Owner::$events[] = 'receiver';

    return $owner;
}

function calls(InheritedOwner $owner, ?Owner $nullable, Action $action): array
{
    $owner->mountedActionHasForm();
    $owner->mountedActionHasForm(mountedAction: $action);
    $owner->mountedActionHasForm(...['mountedAction' => $action]);
    $owner?->mountedActionHasForm($action);
    $owner?->updatedToggledTableColumns(effect('nonnull-extra'));
    computed($owner)->identity()->mountedActionHasForm($action, effect('has-extra'));
    $nullable?->mountedActionHasForm(effect('null-has'))->missing();
    $nullable?->updatedToggledTableColumns(effect('null-columns'))->missing();
    computed($owner)->updatedToggledTableColumns(effect('columns-extra'), effect('reorder-extra'));
    $owner->updatedToggledTableColumns(...[effect('unpacked-extra')]);
    $owner->updatedTableGroupColumn();

    return [computed($owner)->mountedActionHasForm(...), computed($owner)->updatedToggledTableColumns(...)];
}

function customCalls(CustomDescendant $owner): array
{
    $owner->updatedToggledTableColumns();
    $owner->mountedActionHasForm();
    $owner->updatedTableGroupColumn();

    return $owner->inspect();
}

function unrelatedCalls(Unrelated $owner): string
{
    return $owner->updatedToggledTableColumns();
}

function invalidCalls(InheritedOwner $owner): array
{
    $results = [];

    foreach ([
        static fn () => $owner->updatedToggledTableColumns(state: ['ignored']),
        static fn () => $owner->mountedActionHasForm(unknown: null),
        static fn () => $owner->updatedTableGroupColumn(...['unknown' => null]),
    ] as $call) {
        try {
            $call();
            $results[] = 'unexpected success';
        } catch (Throwable $exception) {
            $results[] = $exception::class;
        }
    }

    return $results;
}
