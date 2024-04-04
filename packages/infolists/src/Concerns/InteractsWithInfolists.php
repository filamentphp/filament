<?php

namespace Filament\Infolists\Concerns;

use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Schema\ComponentContainer;

trait InteractsWithInfolists
{
    /**
     * @deprecated Use `getSchema()` instead.
     */
    public function getInfolist(string $name): ?ComponentContainer
    {
        return $this->getSchema($name);
    }

    /**
     * @deprecated Use `cacheSchema()` instead.
     */
    protected function cacheInfolist(string $name, ComponentContainer $infolist): ?ComponentContainer
    {
        return $this->cacheSchema($name, $infolist);
    }

    /**
     * @deprecated Use `getCachedSchemas()` instead.
     *
     * @return array<string, ComponentContainer>
     */
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
    public function callMountedInfolistAction(array $arguments = []): mixed
    {
        return $this->callMountedAction($arguments);
    }

    /**
     * @deprecated Use `mountAction()` instead.
     */
    public function mountInfolistAction(string $name, ?string $component = null, ?string $infolist = null): mixed
    {
        return $this->mountAction($name, context: [
            'schemaComponent' => "{$infolist}.{$component}",
        ]);
    }

    /**
     * @deprecated Use `mountedActionShouldOpenModal()` instead.
     */
    public function mountedInfolistActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return $this->mountedActionShouldOpenModal($mountedAction);
    }

    /**
     * @deprecated Use `mountedActionHasForm()` instead.
     */
    public function mountedInfolistActionHasForm(?Action $mountedAction = null): bool
    {
        return $this->mountedActionHasSchema($mountedAction);
    }

    /**
     * @deprecated Use `getMountedAction()` instead.
     */
    public function getMountedInfolistAction(): ?Action
    {
        return $this->getMountedAction();
    }

    /**
     * @deprecated Use `getMountedActionComponent()` instead.
     */
    public function unmountInfolistAction(bool $shouldCancelParentActions = true): void
    {
        $this->unmountAction($shouldCancelParentActions);
    }

    /**
     * @deprecated Use `makeSchema()` instead.
     */
    protected function makeInfolist(): ComponentContainer
    {
        return Infolist::make($this);
    }
}
