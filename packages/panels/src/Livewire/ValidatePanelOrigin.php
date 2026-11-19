<?php

namespace Filament\Livewire;

use Filament\Facades\Filament;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

use function Livewire\before;

class ValidatePanelOrigin
{
    // Bundled updates verify one snapshot at a time, and newly mounted children inherit its origin.
    protected string | false | null $origin = null;

    protected ?string $componentId = null;

    public static function register(): void
    {
        Livewire::listen('dehydrate', static function (Component $component, ComponentContext $context): void {
            $context->addMemo('filamentOrigin', app(ValidatePanelOrigin::class)->getOrigin());
        });

        before('snapshot-verified', static function (array $snapshot): void {
            app(ValidatePanelOrigin::class)->verifyOrigin($snapshot);
        });

        Livewire::listen('destroy', static function (Component $component): void {
            $validator = app(ValidatePanelOrigin::class);

            if ($validator->componentId === $component->getId()) {
                $validator->componentId = null;
            }
        });

        // Livewire test helpers flush state without ending the container scope.
        Livewire::listen('flush-state', static function (): void {
            $validator = app(ValidatePanelOrigin::class);
            $validator->origin = null;
            $validator->componentId = null;
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

        // Nested snapshots, such as lazy mount parameters, must keep the active component's origin.
        if (filled($this->componentId)) {
            abort_unless($origin === $this->origin, 404);

            return;
        }

        $this->origin = $origin;
        $this->componentId = $snapshot['memo']['id'];
    }
}
