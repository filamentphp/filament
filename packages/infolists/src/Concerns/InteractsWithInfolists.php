<?php

namespace Filament\Infolists\Concerns;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;

trait InteractsWithInfolists /** @phpstan-ignore trait.unused */
{
    /**
     * @deprecated Use `getSchema()` instead.
     */
    #[Unexposed]
    public function getInfolist(string $name): ?Schema
    {
        return $this->getSchema($name);
    }

    /**
     * @deprecated Use `cacheSchema()` instead.
     */
    protected function cacheInfolist(string $name, Schema $schema): ?Schema
    {
        return $this->cacheSchema($name, $schema);
    }

    /**
     * @return array<string, Schema>
     *
     * @deprecated Use `getCachedSchemas()` instead.
     */
    #[Unexposed]
    public function getCachedInfolists(): array
    {
        return $this->getCachedSchemas();
    }

    /**
     * @deprecated Use `hasCachedSchema()` instead.
     */
    protected function hasCachedInfolist(string $name): bool
    {
        return $this->hasCachedSchema($name);
    }

    /**
     * @deprecated Use `callMountedAction()` instead.
     *
     * @param  array<string, mixed>  $arguments
     */
    #[Exposed]
    public function callMountedInfolistAction(array $arguments = []): mixed
    {
        return $this->callMountedAction($arguments);
    }

    /**
     * @deprecated Use `mountAction()` instead.
     */
    #[Exposed]
    public function mountInfolistAction(string $name, ?string $component = null, ?string $infolist = null): mixed
    {
        return $this->mountAction($name, context: [
            'schemaComponent' => "{$infolist}.{$component}",
        ]);
    }

    /**
     * @deprecated Use `mountedActionShouldOpenModal()` instead.
     */
    #[Unexposed]
    public function mountedInfolistActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return $this->mountedActionShouldOpenModal($mountedAction);
    }

    /**
     * @deprecated Use `mountedActionHasForm()` instead.
     */
    #[Unexposed]
    public function mountedInfolistActionHasForm(?Action $mountedAction = null): bool
    {
        return $this->mountedActionHasSchema($mountedAction);
    }

    /**
     * @deprecated Use `getMountedAction()` instead.
     */
    #[Unexposed]
    public function getMountedInfolistAction(): ?Action
    {
        return $this->getMountedAction();
    }

    /**
     * @deprecated Use `getMountedActionComponent()` instead.
     */
    #[Exposed]
    public function unmountInfolistAction(bool $shouldCancelParentActions = true): void
    {
        $this->unmountAction(cancelParentActions: $shouldCancelParentActions ? null : false);
    }
}
