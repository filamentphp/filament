<?php

namespace Filament\Infolists\Concerns;

use Filament\Actions\Action;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;

trait InteractsWithInfolists /** @phpstan-ignore trait.unused */
{
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
     * @deprecated Use `mountedActionHasSchema()` instead.
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
     * @deprecated Use `unmountAction()` instead.
     */
    #[Exposed]
    public function unmountInfolistAction(bool $shouldCancelParentActions = true): void
    {
        $this->unmountAction(cancelParentActions: $shouldCancelParentActions ? null : false);
    }
}
