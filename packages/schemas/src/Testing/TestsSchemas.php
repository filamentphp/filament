<?php

namespace Filament\Schemas\Testing;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasSchemas instance()
 *
 * @mixin Testable
 */
class TestsSchemas
{
    public function assertSchemaComponentExists(): Closure
    {
        return function (string $key, ?string $schema = null, ?Closure $checkComponentUsing = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaExists($schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            $componentInstance = $schemaInstance->getFlatComponents(withHidden: true)[$key] ?? null;

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Component::class,
                $componentInstance,
                "Failed asserting that a component [{$key}] exists on the schema with the name [{$schema}] on the [{$livewireClass}] component."
            );

            if ($checkComponentUsing) {
                Assert::assertTrue(
                    $checkComponentUsing($componentInstance),
                    "Failed asserting that a component [{$key}] and provided configuration exists on the schema with the name [{$schema}] on the [{$livewireClass}] component."
                );
            }

            return $this;
        };
    }

    public function assertSchemaComponentDoesNotExist(): Closure
    {
        return function (string $key, ?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaExists($schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            $componentInstance = $schemaInstance->getFlatComponents(withHidden: true)[$key] ?? null;

            $livewireClass = $this->instance()::class;

            Assert::assertNull(
                $componentInstance,
                "Failed asserting that a component [{$key}] does not exist on the schema with the name [{$schema}] on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertSchemaExists(): Closure
    {
        return function (string $name): static {
            /** @var Schema $schema */
            $schema = $this->instance()->{$name};

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Schema::class,
                $schema,
                "Failed asserting that a schema with the name [{$name}] exists on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertSchemaStateSet(): Closure
    {
        return function (array | Closure $state, ?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaExists($schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            $schemaStatePath = $schemaInstance->getStatePath();

            if ($state instanceof Closure) {
                $state = $state($schemaInstance->getRawState());
            }

            if (is_array($state)) {
                $livewireClass = $this->instance()::class;

                $components = $schemaInstance->getFlatComponents(withActions: false, withHidden: true);

                foreach ($state as $key => $value) {
                    if (array_key_exists($key, $components)) {
                        Assert::assertEquals(
                            $value,
                            $components[$key]->getState(),
                            "Failed asserting that a component [{$key}] has the expected state in the [{$schema}] schema on the [{$livewireClass}] component."
                        );
                    } else {
                        $this->assertSet((filled($schemaStatePath) ? "{$schemaStatePath}." : '') . $key, $value);
                    }
                }
            }

            return $this;
        };
    }

    public function assertSchemaComponentStateSet(): Closure
    {
        return function (string $key, mixed $state, ?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaComponentExists($key, $schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            $componentInstance = $schemaInstance->getFlatComponents(withHidden: true)[$key] ?? null;

            $livewireClass = $this->instance()::class;

            Assert::assertEquals(
                $state,
                $componentInstance->getState(),
                "Failed asserting that a component [{$key}] has the expected state in the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertSchemaComponentStateNotSet(): Closure
    {
        return function (string $key, mixed $state, ?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaComponentExists($key, $schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            $componentInstance = $schemaInstance->getFlatComponents(withHidden: true)[$key] ?? null;

            $livewireClass = $this->instance()::class;

            Assert::assertNotEquals(
                $state,
                $componentInstance->getState(),
                "Failed asserting that a component [{$key}] does not have the expected state in the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertWizardStepExists(): Closure
    {
        return function (int $step, ?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaExists($schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            /** @var Wizard $wizard */
            $wizard = $schemaInstance->getComponent(fn (Component | Action | ActionGroup $component): bool => $component instanceof Wizard);
            Assert::assertArrayHasKey(
                $step - 1,
                $wizard->getDefaultChildComponents(),
                "Wizard does not have a step [{$step}]."
            );

            return $this;
        };
    }

    public function assertWizardCurrentStep(): Closure
    {
        return function (int $step, ?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaExists($schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            /** @var Wizard $wizard */
            $wizard = $schemaInstance->getComponent(fn (Component | Action | ActionGroup $component): bool => $component instanceof Wizard);
            Assert::assertEquals(
                $step,
                $current = $wizard->getCurrentStepIndex() + 1,
                "Failed asserting that wizard is on step [{$step}], current step is [{$current}]."
            );

            return $this;
        };
    }

    public function goToWizardStep(): Closure
    {
        return function (int $step, ?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertWizardStepExists($step, $schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            /** @var Wizard $wizard */
            $wizard = $schemaInstance->getComponent(fn (Component | Action | ActionGroup $component): bool => $component instanceof Wizard);

            $stepIndex = ($step <= 1) ? 0 : $step - 2;

            $this->call('callSchemaComponentMethod', $wizard->getKey(), 'nextStep', [$stepIndex]);

            return $this;
        };
    }

    public function goToNextWizardStep(): Closure
    {
        return function (?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaExists($schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            /** @var Wizard $wizard */
            $wizard = $schemaInstance->getComponent(fn (Component | Action | ActionGroup $component): bool => $component instanceof Wizard);

            $this->call('callSchemaComponentMethod', $wizard->getKey(), 'nextStep', [$wizard->getCurrentStepIndex()]);

            return $this;
        };
    }

    public function goToPreviousWizardStep(): Closure
    {
        return function (?string $schema = null): static {
            if ($this->instance() instanceof HasActions) {
                $schema ??= $this->instance()->getMountedActionSchemaName();
            }

            $schema ??= $this->instance()->getDefaultTestingSchemaName();

            /** @phpstan-ignore-next-line */
            $this->assertSchemaExists($schema);

            /** @var Schema $schemaInstance */
            $schemaInstance = $this->instance()->{$schema};

            /** @var Wizard $wizard */
            $wizard = $schemaInstance->getComponent(fn (Component | Action | ActionGroup $component): bool => $component instanceof Wizard);

            $this->call('callSchemaComponentMethod', $wizard->getKey(), 'previousStep', [$wizard->getCurrentStepIndex()]);

            return $this;
        };
    }
}
