<?php

namespace Filament\Forms\Testing;

use BackedEnum;
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

    public function setFormComponentActionData(): Closure
    {
        return function (array $data): static {
            $this->fillForm($data);

            return $this;
        };
    }

    public function assertFormComponentActionDataSet(): Closure
    {
        return function (array | Closure $data): static {
            $this->assertSchemaStateSet($data);

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

    public function assertFormComponentActionExists(): Closure
    {
        return function (string | array $components, string | array $actions, string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);

            $this->assertActionExists($actions);

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotExist(): Closure
    {
        return function (string | array $components, string | array $actions, string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);

            $this->assertActionDoesNotExist($actions);

            return $this;
        };
    }

    public function assertFormComponentActionVisible(): Closure
    {
        return function (string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionVisible($actions);

            return $this;
        };
    }

    public function assertFormComponentActionHidden(): Closure
    {
        return function (string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionHidden($actions);

            return $this;
        };
    }

    public function assertFormComponentActionEnabled(): Closure
    {
        return function (string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionEnabled($actions);

            return $this;
        };
    }

    public function assertFormComponentActionDisabled(): Closure
    {
        return function (string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionDisabled($actions);

            return $this;
        };
    }

    public function assertFormComponentActionHasIcon(): Closure
    {
        return function (string | array $components, string | array $actions, string | BackedEnum $icon, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionHasIcon($actions, $icon);

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveIcon(): Closure
    {
        return function (string | array $components, string | array $actions, string | BackedEnum $icon, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionDoesNotHaveIcon($actions, $icon);

            return $this;
        };
    }

    public function assertFormComponentActionHasLabel(): Closure
    {
        return function (string | array $components, string | array $actions, string $label, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionHasLabel($actions, $label);

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveLabel(): Closure
    {
        return function (string | array $components, string | array $actions, string $label, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionDoesNotHaveLabel($actions, $label);

            return $this;
        };
    }

    public function assertFormComponentActionHasColor(): Closure
    {
        return function (string | array $components, string | array $actions, string | array $color, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionHasColor($actions, $color);

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveColor(): Closure
    {
        return function (string | array $components, string | array $actions, string | array $color, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionDoesNotHaveColor($actions, $color);

            return $this;
        };
    }

    public function assertFormComponentActionHasUrl(): Closure
    {
        return function (string | array $components, string | array $actions, string $url, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionHasUrl($actions, $url);

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveUrl(): Closure
    {
        return function (string | array $components, string | array $actions, string $url, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionDoesNotHaveUrl($actions, $url);

            return $this;
        };
    }

    public function assertFormComponentActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionShouldOpenUrlInNewTab($actions);

            return $this;
        };
    }

    public function assertFormComponentActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);

            $this->assertActionShouldNotOpenUrlInNewTab($actions);

            return $this;
        };
    }

    public function assertFormComponentActionMounted(): Closure
    {
        return function (string | array $components, string | array $actions, string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);

            $this->assertActionMounted($actions);

            return $this;
        };
    }

    public function assertFormComponentActionNotMounted(): Closure
    {
        return function (string | array $components, string | array $actions, string $formName = 'form'): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);

            $this->assertActionNotMounted($actions);

            return $this;
        };
    }

    public function assertFormComponentActionHalted(): Closure
    {
        return $this->assertFormComponentActionMounted();
    }

    public function assertHasFormComponentActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasFormErrors($keys);

            return $this;
        };
    }

    public function assertHasNoFormComponentActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoFormErrors($keys);

            return $this;
        };
    }

    public function parseNestedFormComponentActions(): Closure
    {
        return function (string | array $components, string | array $actions, string $form, array $arguments = []): array {
            $this->assertFormExists($form);

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
