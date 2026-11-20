<?php

namespace Filament\Livewire;

use Filament\Facades\Filament;
use Filament\Http\Middleware\SetUpPanel;
use Illuminate\Routing\Route;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Livewire\before;

class ValidatePanelOrigin
{
    // Bundled updates verify one snapshot at a time, and newly mounted children inherit its context.
    protected string | false | null $origin = null;

    /** @var array{panel: string, uri: string, domain: ?string, name: ?string} | false | null */
    protected array | false | null $routeContext = null;

    protected ?string $componentId = null;

    public static function register(): void
    {
        Livewire::listen('dehydrate', static function (Component $component, ComponentContext $context): void {
            $validator = app(ValidatePanelOrigin::class);

            $context->addMemo('filamentOrigin', $validator->getOrigin());
            $context->addMemo('filamentRoute', $validator->getRouteContext());
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
            $validator->routeContext = null;
            $validator->componentId = null;
        });
    }

    protected function getOrigin(): string | false
    {
        if (app(HandleRequests::class)->isLivewireRoute()) {
            abort_if(is_null($this->origin), 404);

            return $this->origin;
        }

        $routeContext = $this->getRouteContext();

        if (($routeContext === false) || (! $routeContext['domain'])) {
            return false;
        }

        return request()->getSchemeAndHttpHost();
    }

    /** @return array{panel: string, uri: string, domain: ?string, name: ?string} | false */
    protected function getRouteContext(): array | false
    {
        if (app(HandleRequests::class)->isLivewireRoute()) {
            abort_if(is_null($this->routeContext), 404);

            return $this->routeContext;
        }

        $route = request()->route();
        $panel = Filament::getCurrentPanel();

        if (
            (! $route)
            || (! $panel)
            || (! $this->hasPanelMiddleware($route, $panel->getId()))
        ) {
            return false;
        }

        return [
            'panel' => $panel->getId(),
            'uri' => $route->uri(),
            'domain' => $route->getDomain(),
            'name' => $route->getName(),
        ];
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

        $routeContext = $snapshot['memo']['filamentRoute'] ?? null;

        abort_unless(($routeContext === false) || is_array($routeContext), 404);

        if (is_array($routeContext)) {
            $this->verifyRoute($snapshot, $routeContext);
        }

        // Nested snapshots, such as lazy mount parameters, must keep the active component's context.
        if (filled($this->componentId)) {
            abort_unless(($origin === $this->origin) && ($routeContext === $this->routeContext), 404);

            return;
        }

        $this->origin = $origin;
        $this->routeContext = $routeContext;
        $this->componentId = $snapshot['memo']['id'];
    }

    protected function hasPanelMiddleware(Route $route, string $panelId): bool
    {
        return in_array(
            SetUpPanel::class . ":{$panelId}",
            app('router')->gatherRouteMiddleware($route),
            true,
        );
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @param  array<string, mixed>  $routeContext
     */
    protected function verifyRoute(array $snapshot, array $routeContext): void
    {
        $path = $snapshot['memo']['path'] ?? null;
        $method = $snapshot['memo']['method'] ?? null;
        $panelId = $routeContext['panel'] ?? null;

        abort_unless(is_string($path) && is_string($method) && is_string($panelId), 404);

        $currentPath = '/' . ltrim(request()->path(), '/');
        $originalPath = '/' . ltrim($path, '/');
        $serverBag = clone request()->server;
        $serverBag->set('REQUEST_URI', str_replace($currentPath, $originalPath, $serverBag->get('REQUEST_URI')));
        $serverBag->set('REQUEST_METHOD', $method);

        $request = request()->duplicate(server: $serverBag->all());

        try {
            $route = app('router')->getRoutes()->match($request);
        } catch (MethodNotAllowedHttpException | NotFoundHttpException) {
            abort(404);
        }

        abort_unless(
            $this->hasPanelMiddleware($route, $panelId) &&
            ($routeContext === [
                'panel' => $panelId,
                'uri' => $route->uri(),
                'domain' => $route->getDomain(),
                'name' => $route->getName(),
            ]),
            404,
        );
    }
}
