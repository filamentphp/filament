<?php

namespace Filament\Infolists\Testing;

use Closure;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component&HasSchemas instance()
 *
 * @mixin Testable
 */
class TestsInfolistActions
{
    public function mountInfolistAction(): Closure
    {
        return function (string $component, string | array $actions, string $schema = 'infolist'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedInfolistActions($component, $actions, $schema);

            $this->mountAction($actions);

            return $this;
        };
    }

    public function unmountInfolistAction(): Closure
    {
        return function (): static {
            $this->unmountAction();

            return $this;
        };
    }

    public function callInfolistAction(): Closure
    {
        return function (string $component, string | array $actions, array $data = [], array $arguments = [], string $schema = 'infolist'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedInfolistActions($component, $actions, $schema, $arguments);

            $this->callAction($actions, $data);

            return $this;
        };
    }

    public function callMountedInfolistAction(): Closure
    {
        return function (array $arguments = []): static {
            $this->callMountedAction($arguments);

            return $this;
        };
    }

    public function parseNestedInfolistActions(): Closure
    {
        return function (string $component, string | array $actions, string $infolist, array $arguments = []): array {
            /** @var array<array<string, mixed>> $actions */
            $actions = $this->parseNestedActions($actions, $arguments);

            $actions[array_key_first($actions)]['context']['schemaComponent'] = "{$infolist}.{$component}";

            return $actions;
        };
    }
}
