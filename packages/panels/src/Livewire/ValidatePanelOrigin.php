<?php

namespace Filament\Livewire;

use Filament\Facades\Filament;
use Filament\Http\Middleware\SetUpPanel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleComponents\Checksum;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

use function Livewire\after;
use function Livewire\before;

class ValidatePanelOrigin
{
    // Bundled updates verify one snapshot at a time, and newly mounted children inherit its context.
    protected string | false | null $panelContext = null;

    /** @var array{model: class-string<Model>, key: string} | false | null */
    protected array | false | null $tenantContext = null;

    /** @var array{page: ?string, resource: ?string} | false | null */
    protected array | false | null $configurationContext = null;

    /**
     * @var array{
     *     panel: string | false,
     *     tenant: array{model: class-string<Model>, key: string} | false,
     * } | null
     */
    protected ?array $requestContext = null;

    protected ?string $componentId = null;

    public static function register(): void
    {
        Livewire::listen('dehydrate', static function (Component $component, ComponentContext $context): void {
            $validator = app(ValidatePanelOrigin::class);

            $context->addMemo('filamentPanel', $validator->getPanelContext());
            $context->addMemo('filamentTenant', $validator->getTenantContext());
            $context->addMemo('filamentConfiguration', $validator->getConfigurationContext());
        });

        Livewire::listen('request', static function (array $requestPayload): void {
            app(ValidatePanelOrigin::class)->preflightRequest($requestPayload);
        });

        before('snapshot-verified', static function (array $snapshot): void {
            app(ValidatePanelOrigin::class)->verifyContext($snapshot);
        });

        // Persistent middleware restores the panel, tenant, and configuration in its `snapshot-verified` listener.
        after('snapshot-verified', static function (array $snapshot): void {
            app(ValidatePanelOrigin::class)->verifyRestoredContext($snapshot);
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
            $validator->panelContext = null;
            $validator->tenantContext = null;
            $validator->configurationContext = null;
            $validator->requestContext = null;
            $validator->componentId = null;
        });
    }

    /** @param array<mixed> $requestPayload */
    protected function preflightRequest(array $requestPayload): void
    {
        if (! app(HandleRequests::class)->isLivewireRoute()) {
            return;
        }

        foreach ($requestPayload as $componentPayload) {
            abort_unless(is_array($componentPayload), 404);

            $encodedSnapshot = $componentPayload['snapshot'] ?? null;

            abort_unless(is_string($encodedSnapshot), 404);

            $snapshot = json_decode($encodedSnapshot, associative: true);

            abort_unless(is_array($snapshot), 404);
            abort_unless(is_string($snapshot['checksum'] ?? null), 404);

            Checksum::verify($snapshot);

            $snapshotContext = $this->getSnapshotContext($snapshot);
            ['panel' => $panelContext, 'tenant' => $tenantContext] = $snapshotContext;

            $this->preflightLazySnapshots($componentPayload['calls'] ?? [], $snapshotContext);

            $requestContext = [
                'panel' => $panelContext,
                'tenant' => $tenantContext,
            ];

            if ($this->requestContext === null) {
                $this->requestContext = $requestContext;

                continue;
            }

            abort_unless($requestContext === $this->requestContext, 404);
        }
    }

    /**
     * @param  array<string, mixed>  $componentContext
     */
    protected function preflightLazySnapshots(mixed $calls, array $componentContext): void
    {
        abort_unless(is_array($calls), 404);

        foreach ($calls as $call) {
            if ((! is_array($call)) || (($call['method'] ?? null) !== '__lazyLoad')) {
                continue;
            }

            $parameters = $call['params'] ?? null;

            abort_unless(is_array($parameters), 404);

            $encodedSnapshot = $parameters[0] ?? null;

            abort_unless(is_string($encodedSnapshot), 404);

            $decodedSnapshot = base64_decode($encodedSnapshot, strict: true);

            abort_unless(is_string($decodedSnapshot), 404);

            $snapshot = json_decode($decodedSnapshot, associative: true);

            abort_unless(is_array($snapshot), 404);
            abort_unless(is_string($snapshot['checksum'] ?? null), 404);

            Checksum::verify($snapshot);

            abort_unless($this->getSnapshotContext($snapshot) === $componentContext, 404);
        }
    }

    protected function getPanelContext(): string | false
    {
        if (app(HandleRequests::class)->isLivewireRoute()) {
            abort_if(is_null($this->panelContext), 404);

            return $this->panelContext;
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

        return $panel->getId();
    }

    /** @return array{model: class-string<Model>, key: string} | false */
    protected function getTenantContext(): array | false
    {
        if (app(HandleRequests::class)->isLivewireRoute()) {
            abort_if(is_null($this->tenantContext), 404);

            return $this->tenantContext;
        }

        if ($this->getPanelContext() === false) {
            return false;
        }

        $tenant = Filament::getTenant();

        if (! $tenant) {
            return false;
        }

        return [
            'model' => $tenant::class,
            'key' => (string) $tenant->getKey(),
        ];
    }

    /** @return array{page: ?string, resource: ?string} | false */
    protected function getConfigurationContext(): array | false
    {
        if (app(HandleRequests::class)->isLivewireRoute()) {
            abort_if(is_null($this->configurationContext), 404);

            return $this->configurationContext;
        }

        if ($this->getPanelContext() === false) {
            return false;
        }

        return [
            'page' => Filament::getCurrentPageConfigurationKey(),
            'resource' => Filament::getCurrentResourceConfigurationKey(),
        ];
    }

    /** @param array<string, mixed> $snapshot */
    protected function verifyContext(array $snapshot): void
    {
        if (! app(HandleRequests::class)->isLivewireRoute()) {
            return;
        }

        [
            'panel' => $panelContext,
            'tenant' => $tenantContext,
            'configuration' => $configurationContext,
        ] = $this->getSnapshotContext($snapshot);

        // Nested snapshots, such as lazy mount parameters, must keep the active component's context.
        if (filled($this->componentId)) {
            abort_unless(
                ($panelContext === $this->panelContext) &&
                ($tenantContext === $this->tenantContext) &&
                ($configurationContext === $this->configurationContext),
                404,
            );

            return;
        }

        $requestContext = [
            'panel' => $panelContext,
            'tenant' => $tenantContext,
        ];

        if ($this->requestContext === null) {
            $this->requestContext = $requestContext;
        } else {
            abort_unless($requestContext === $this->requestContext, 404);
        }

        $this->panelContext = $panelContext;
        $this->tenantContext = $tenantContext;
        $this->configurationContext = $configurationContext;
        $this->componentId = $snapshot['memo']['id'];
    }

    /** @param array<string, mixed> $snapshot */
    protected function verifyRestoredContext(array $snapshot): void
    {
        if (! app(HandleRequests::class)->isLivewireRoute()) {
            return;
        }

        $panelContext = $snapshot['memo']['filamentPanel'] ?? null;

        if (! is_string($panelContext)) {
            return;
        }

        $panel = Filament::getCurrentPanel();

        abort_unless($panel?->getId() === $panelContext, 404);

        $tenantContext = $snapshot['memo']['filamentTenant'] ?? null;
        $tenant = Filament::getTenant();
        $restoredTenantContext = $tenant ? [
            'model' => $tenant::class,
            'key' => (string) $tenant->getKey(),
        ] : false;

        abort_unless($restoredTenantContext === $tenantContext, 404);

        $configurationContext = $snapshot['memo']['filamentConfiguration'] ?? null;
        $restoredConfigurationContext = [
            'page' => Filament::getCurrentPageConfigurationKey(),
            'resource' => Filament::getCurrentResourceConfigurationKey(),
        ];

        abort_unless($restoredConfigurationContext === $configurationContext, 404);
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array{
     *     panel: string | false,
     *     tenant: array{model: class-string<Model>, key: string} | false,
     *     configuration: array{page: ?string, resource: ?string} | false,
     * }
     */
    protected function getSnapshotContext(array $snapshot): array
    {
        $memo = $snapshot['memo'] ?? null;

        abort_unless(is_array($memo), 404);

        $panelContext = $memo['filamentPanel'] ?? null;

        abort_unless(($panelContext === false) || is_string($panelContext), 404);

        $tenantContext = $memo['filamentTenant'] ?? null;

        abort_unless(($tenantContext === false) || is_array($tenantContext), 404);

        if (is_array($tenantContext)) {
            abort_unless(
                (array_keys($tenantContext) === ['model', 'key']) &&
                is_string($tenantContext['model']) &&
                is_a($tenantContext['model'], Model::class, true) &&
                is_string($tenantContext['key']),
                404,
            );
        }

        abort_if(($panelContext === false) && ($tenantContext !== false), 404);

        $configurationContext = $memo['filamentConfiguration'] ?? null;

        abort_unless(($configurationContext === false) || is_array($configurationContext), 404);

        if (is_array($configurationContext)) {
            abort_unless(
                (array_keys($configurationContext) === ['page', 'resource']) &&
                (is_null($configurationContext['page']) || is_string($configurationContext['page'])) &&
                (is_null($configurationContext['resource']) || is_string($configurationContext['resource'])),
                404,
            );
        }

        abort_unless(($panelContext === false) === ($configurationContext === false), 404);

        return [
            'panel' => $panelContext,
            'tenant' => $tenantContext,
            'configuration' => $configurationContext,
        ];
    }

    protected function hasPanelMiddleware(Route $route, string $panelId): bool
    {
        return in_array(
            SetUpPanel::class . ":{$panelId}",
            app('router')->gatherRouteMiddleware($route),
            true,
        );
    }
}
