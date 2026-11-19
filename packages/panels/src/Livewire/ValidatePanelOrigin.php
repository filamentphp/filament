<?php

namespace Filament\Livewire;

use Filament\Facades\Filament;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

use function Livewire\before;

class ValidatePanelOrigin
{
    // Bundled updates verify one snapshot at a time, and newly mounted children inherit its origin.
    protected string | false | null $origin = null;

    public static function register(): void
    {
        Livewire::listen('dehydrate', static function ($component, ComponentContext $context): void {
            $context->addMemo('filamentOrigin', app(ValidatePanelOrigin::class)->getOrigin());
        });

        before('snapshot-verified', static function (array $snapshot): void {
            app(ValidatePanelOrigin::class)->verifyOrigin($snapshot);
        });

        // Livewire test helpers flush state without ending the container scope.
        Livewire::listen('flush-state', static function (): void {
            app(ValidatePanelOrigin::class)->origin = null;
        });
    }

    protected function getOrigin(): string | false
    {
        if (app(HandleRequests::class)->isLivewireRoute()) {
            abort_if(is_null($this->origin), 404);

            return $this->origin;
        }

        $route = request()->route();
        $panel = Filament::getCurrentPanel();

        if (
            (! $route)
            || (! $route->getDomain())
            || (! $panel)
            || (! in_array("panel:{$panel->getId()}", $route->gatherMiddleware(), true))
        ) {
            return false;
        }

        return request()->getSchemeAndHttpHost();
    }

    /** @param array<string, mixed> $snapshot */
    protected function verifyOrigin(array $snapshot): void
    {
        if (! app(HandleRequests::class)->isLivewireRoute()) {
            return;
        }

        $origin = $snapshot['memo']['filamentOrigin'] ?? null;

        abort_unless(($origin === false) || is_string($origin), 404);

        if (is_string($origin)) {
            abort_unless($origin === request()->getSchemeAndHttpHost(), 404);
        }

        $this->origin = $origin;
    }
}
