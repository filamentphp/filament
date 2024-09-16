<?php

namespace Filament\Forms\Testing;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

use function Livewire\store;

/**
 * @method HasForms instance()
 *
 * @mixin Testable
 */
class TestsComponentActions
{
    public function mountFormComponentAction(): Closure
    {
        return function (string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            [$component, $name] = $this->parseNestedFormComponentActionComponentAndName($component, $name, $formName);

            foreach ($name as $actionNestingIndex => $actionName) {
                $this->call(
                    'mountFormComponentAction',
                    $component[$actionNestingIndex],
                    $actionName,
                    $arguments,
                );
            }

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if (! count($this->instance()->mountedFormComponentActions)) {
                $this->assertNotDispatched('open-modal');

                return $this;
            }

            $this->assertSet('mountedFormComponentActionsComponents', $component);
            $this->assertSet('mountedFormComponentActions', $name);

            $this->assertDispatched('open-modal', id: "{$this->instance()->getId()}-form-component-action");

            return $this;
        };
    }

    public function unmountFormComponentAction(): Closure
    {
        return function (): static {
            $this->call('unmountFormComponentAction');

            return $this;
        };
    }

    public function setFormComponentActionData(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedFormComponentActionsData.' . array_key_last($this->instance()->mountedFormComponentActionsData) . '.') as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        };
    }

    public function assertFormComponentActionDataSet(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedFormComponentActionsData.' . array_key_last($this->instance()->mountedFormComponentActionsData) . '.') as $key => $value) {
                $this->assertSet($key, $value);
            }

            return $this;
        };
    }

    public function callFormComponentAction(): Closure
    {
        return function (string | array $component, string | array $name, array $data = [], array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionVisible($component, $name, $arguments, $formName);

            /** @phpstan-ignore-next-line */
            $this->mountFormComponentAction($component, $name, $arguments, $formName);

            if (! $this->instance()->getMountedFormComponentAction()) {
                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setFormComponentActionData($data);

            /** @phpstan-ignore-next-line */
            $this->callMountedFormComponentAction($arguments);

            return $this;
        };
    }

    public function callMountedFormComponentAction(): Closure
    {
        return function (array $arguments = []): static {
            $action = $this->instance()->getMountedFormComponentAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedFormComponentAction', $arguments);

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if (! count($this->instance()->mountedFormComponentActions)) {
                $this->assertDispatched('close-modal', id: "{$this->instance()->getId()}-form-component-action");
            }

            return $this;
        };
    }

    public function assertFormComponentActionExists(): Closure
    {
        return function (string | array $component, string | array $name, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertInstanceOf(
                Action::class,
                $action,
                message: "Failed asserting that a form component action with name [{$prettyName}] is registered to [{$component->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotExist(): Closure
    {
        return function (string | array $component, string | array $name, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertNull(
                $action,
                message: "Failed asserting that a form component action with name [{$prettyName}] is not registered to [{$component->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionVisible(): Closure
    {
        return function (string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->isHidden(),
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionHidden(): Closure
    {
        return function (string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->isHidden(),
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionEnabled(): Closure
    {
        return function (string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->isDisabled(),
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionDisabled(): Closure
    {
        return function (string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->isEnabled(),
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionHasIcon(): Closure
    {
        return function (string | array $component, string | array $name, string $icon, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->getIcon() === $icon,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], has icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveIcon(): Closure
    {
        return function (string | array $component, string | array $name, string $icon, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->getIcon() === $icon,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], does not have icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionHasLabel(): Closure
    {
        return function (string | array $component, string | array $name, string $label, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->getLabel() === $label,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], has label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveLabel(): Closure
    {
        return function (string | array $component, string | array $name, string $label, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->getLabel() === $label,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], does not have label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionHasColor(): Closure
    {
        return function (string | array $component, string | array $name, string | array $color, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            $colorName = is_string($color) ? $color : 'custom';

            Assert::assertTrue(
                $action->getColor() === $color,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], has [{$colorName}] color on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveColor(): Closure
    {
        return function (string | array $component, string | array $name, string | array $color, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            $colorName = is_string($color) ? $color : 'custom';

            Assert::assertFalse(
                $action->getColor() === $color,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], does not have [{$colorName}] color on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionHasUrl(): Closure
    {
        return function (string | array $component, string | array $name, string $url, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->getUrl() === $url,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], has URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionDoesNotHaveUrl(): Closure
    {
        return function (string | array $component, string | array $name, string $url, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->getUrl() === $url,
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], does not have URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], should open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedFormComponentActionComponentAndName($component, $name, $formName, $arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that a form component action with name [{$prettyName}], registered to [{$component->getKey()}], should not open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertFormComponentActionMounted(): Closure
    {
        return function (string | array $component, string | array $name, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $name] = $this->parseNestedFormComponentActionComponentAndName($component, $name, $formName);

            $this->assertSet('mountedFormComponentActions', $name);

            return $this;
        };
    }

    public function assertFormComponentActionNotMounted(): Closure
    {
        return function (string | array $component, string | array $name, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line */
            $this->assertFormComponentActionExists($component, $name, $formName);

            /** @phpstan-ignore-next-line */
            [$component, $name] = $this->parseNestedFormComponentActionComponentAndName($component, $name, $formName);

            $this->assertNotSet('mountedFormComponentActions', $name);

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
            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => 'mountedFormComponentActionsData.' . array_key_last($this->instance()->mountedFormComponentActionsData) . '.' . $value];
                        }

                        return ['mountedFormComponentActionsData.' . array_key_last($this->instance()->mountedFormComponentActionsData) . '.' . $key => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertHasNoFormComponentActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => 'mountedFormComponentActionsData.' . array_key_last($this->instance()->mountedFormComponentActionsData) . '.' . $value];
                        }

                        return ['mountedFormComponentActionsData.' . array_key_last($this->instance()->mountedFormComponentActionsData) . '.' . $key => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function getNestedFormComponentActionComponentAndName(): Closure
    {
        return function (string | array $component, string | array $name, string $formName = 'form', array $arguments = []): array {
            $isSingular = ! is_array($component);

            /** @phpstan-ignore-next-line */
            [$component, $name] = $this->parseNestedFormComponentActionComponentAndName($component, $name, $formName);

            foreach ($component as $componentNestingIndex => $componentKey) {
                foreach ($this->instance()->getCachedForms() as $form) {
                    $formComponent = $form->getComponent($componentKey);

                    if (! $formComponent) {
                        continue;
                    }

                    break;
                }

                $action = ($formComponent ?? null)?->getAction($name[$componentNestingIndex]);
                $action?->arguments($isSingular ? $arguments : $arguments[$componentNestingIndex] ?? []);
            }

            return [$formComponent ?? null, $action ?? null];
        };
    }

    public function parseNestedFormComponentActionComponentAndName(): Closure
    {
        return function (string | array $component, string | array $name, string $formName = 'form'): array {
            $this->assertFormExists($formName);

            $component = Arr::wrap($component);

            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $components = [];

            foreach ($component as $componentIndex => $componentKey) {
                if ($componentIndex) {
                    $components[] = 'mountedFormComponentActionData.' . ($componentIndex - 1) . ".{$componentKey}";

                    continue;
                }

                $components[] = "{$form->getStatePath()}.{$componentKey}";
            }

            return [$components, $this->parseNestedActionName($name)];
        };
    }
}
