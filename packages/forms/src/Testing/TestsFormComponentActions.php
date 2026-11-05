<?php

namespace Filament\Forms\Testing;

use Closure;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component&HasSchemas instance()
 *
 * @mixin Testable
 */
class TestsFormComponentActions
{
    public function mountFormComponentAction(): Closure
    {
        return function (string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->mountAction($actions);

            return $this;
        };
    }

    public function unmountFormComponentAction(): Closure
    {
        return function (): static {
            $this->unmountAction();

            return $this;
        };
    }

    public function callFormComponentAction(): Closure
    {
        return function (string | array $components, string | array $actions, array $data = [], array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->callAction($actions, $data);

            return $this;
        };
    }

    public function callMountedFormComponentAction(): Closure
    {
        return function (array $arguments = []): static {
            $this->callMountedAction($arguments);

            return $this;
        };
    }

    public function parseNestedFormComponentActions(): Closure
    {
        return function (string | array $components, string | array $actions, string $form, array $arguments = []): array {
            $this->assertSchemaExists($form);

            /** @var array<array<string, mixed>> $actions */
            $actions = $this->parseNestedActions($actions, $arguments);

            $components = Arr::wrap($components);

            foreach ($actions as $actionNestingIndex => $action) {
                $component = $components[$actionNestingIndex] ?? null;

                if (blank($component)) {
                    continue;
                }

                if (! $actionNestingIndex) {
                    $component = "{$form}.{$component}";
                }

                $action['context']['schemaComponent'] = $component;

                $actions[$actionNestingIndex] = $action;
            }

            return $actions;
        };
    }
}
