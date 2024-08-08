<?php

namespace Filament\Forms\Testing;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasForms instance()
 *
 * @mixin Testable
 */
class TestsForms
{
    public function fillForm(): Closure
    {
        return function (array | Closure $state = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $formStatePath = $form->getStatePath();

            if ($state instanceof Closure) {
                $state = $state($form->getRawState());
            }

            if (is_array($state)) {
                $state = Arr::undot($state);

                if (filled($formStatePath)) {
                    $state = Arr::undot([$formStatePath => $state]);
                }

                foreach (Arr::dot($state) as $key => $value) {
                    if ($value instanceof UploadedFile ||
                        (is_array($value) && isset($value[0]) && $value[0] instanceof UploadedFile)
                    ) {
                        $this->set($key, $value);
                        Arr::set($state, $key, $this->get($key));
                    }
                }

                $this->call('fillFormDataForTesting', $state);
            }

            $this->refresh();

            return $this;
        };
    }

    public function assertFormSet(): Closure
    {
        return function (array | Closure $state, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $formStatePath = $form->getStatePath();

            if ($state instanceof Closure) {
                $state = $state($form->getRawState());
            }

            if (is_array($state)) {
                foreach (Arr::dot($state, prepend: filled($formStatePath) ? "{$formStatePath}." : '') as $key => $value) {
                    $this->assertSet($key, $value);
                }
            }

            return $this;
        };
    }

    public function assertHasFormErrors(): Closure
    {
        return function (array $keys = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $formStatePath = $form->getStatePath();

            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key) use ($formStatePath): array {
                        if (is_int($key)) {
                            return [$key => (filled($formStatePath) ? "{$formStatePath}.{$value}" : $value)];
                        }

                        return [(filled($formStatePath) ? "{$formStatePath}.{$key}" : $key) => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertHasNoFormErrors(): Closure
    {
        return function (array $keys = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $formStatePath = $form->getStatePath();

            $this->assertHasNoErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key) use ($formStatePath): array {
                        if (is_int($key)) {
                            return [$key => (filled($formStatePath) ? "{$formStatePath}.{$value}" : $value)];
                        }

                        return [(filled($formStatePath) ? "{$formStatePath}.{$key}" : $key) => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertFormExists(): Closure
    {
        return function (string $name = 'form'): static {
            /** @var ComponentContainer $form */
            $form = $this->instance()->{$name};

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                ComponentContainer::class,
                $form,
                "Failed asserting that a form with the name [{$name}] exists on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertFormComponentExists(): Closure
    {
        return function (string $componentKey, string | Closure $formName = 'form', ?Closure $checkComponentUsing = null): static {
            if ($formName instanceof Closure) {
                $checkComponentUsing = $formName;
                $formName = 'form';
            }

            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var ?Component $component */
            $component = $form->getFlatComponentsByKey(withHidden: true)[$componentKey] ?? null;

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Component::class,
                $component,
                "Failed asserting that a component with the key [{$componentKey}] exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
            );

            if ($checkComponentUsing) {
                Assert::assertTrue(
                    $checkComponentUsing($component),
                    "Failed asserting that a component with the key [{$componentKey}] and provided configuration exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
                );
            }

            return $this;
        };
    }

    public function assertFormComponentDoesNotExist(): Closure
    {
        return function (string $componentKey, string $formName = 'form'): static {
            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            $components = $form->getFlatComponentsByKey(withHidden: true);

            $livewireClass = $this->instance()::class;

            Assert::assertArrayNotHasKey(
                $componentKey,
                $components,
                "Failed asserting that a component with the key [{$componentKey}] does not exist on the form named [{$formName}] on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertFormFieldExists(): Closure
    {
        return function (string $fieldName, string | Closure $formName = 'form', ?Closure $checkFieldUsing = null): static {
            if ($formName instanceof Closure) {
                $checkFieldUsing = $formName;
                $formName = 'form';
            }

            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var ?Field $field */
            $field = $form->getFlatFields(withHidden: true)[$fieldName] ?? null;

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Field::class,
                $field,
                "Failed asserting that a field with the name [{$fieldName}] exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
            );

            if ($checkFieldUsing) {
                Assert::assertTrue(
                    $checkFieldUsing($field),
                    "Failed asserting that a field with the name [{$fieldName}] and provided configuration exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
                );
            }

            return $this;
        };
    }

    public function assertFormFieldDoesNotExist(): Closure
    {
        return function (string $fieldName, string $formName = 'form'): static {
            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            $fields = $form->getFlatFields(withHidden: false);

            $livewireClass = $this->instance()::class;

            Assert::assertArrayNotHasKey(
                $fieldName,
                $fields,
                "Failed asserting that a field with the name [{$fieldName}] does not exist on the form named [{$formName}] on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertFormFieldIsDisabled(): Closure
    {
        return function (string $fieldName, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormFieldExists($fieldName, $formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var Field $field */
            $field = $form->getFlatFields(withHidden: true)[$fieldName];

            $livewireClass = $this->instance()::class;

            /** @phpstan-ignore-next-line  */
            $this->assertFormFieldExists($fieldName, $formName, function (Field $field) use ($fieldName, $formName, $livewireClass): bool {
                Assert::assertTrue(
                    $field->isDisabled(),
                    "Failed asserting that a field with the name [{$fieldName}] is disabled on the form named [{$formName}] on the [{$livewireClass}] component."
                );

                return true;
            });

            return $this;
        };
    }

    public function assertFormFieldIsEnabled(): Closure
    {
        return function (string $fieldName, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormFieldExists($fieldName, $formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var Field $field */
            $field = $form->getFlatFields(withHidden: true)[$fieldName];

            $livewireClass = $this->instance()::class;

            /** @phpstan-ignore-next-line  */
            $this->assertFormFieldExists($fieldName, $formName, function (Field $field) use ($fieldName, $formName, $livewireClass): bool {
                Assert::assertFalse(
                    $field->isDisabled(),
                    "Failed asserting that a field with the name [{$fieldName}] is enabled on the form named [{$formName}] on the [{$livewireClass}] component."
                );

                return true;
            });

            return $this;
        };
    }

    public function assertFormFieldIsReadOnly(): Closure
    {
        return function (string $fieldName, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormFieldExists($fieldName, $formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var TextInput $field */
            $field = $form->getFlatFields(withHidden: true)[$fieldName];

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $field->isReadOnly(),
                "Failed asserting that a field with the name [{$fieldName}] is read-only on the form named [{$formName}] on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertFormFieldIsHidden(): Closure
    {
        return function (string $fieldName, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormFieldExists($fieldName, $formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            $fields = $form->getFlatFields(withHidden: false);

            $livewireClass = $this->instance()::class;

            Assert::assertArrayNotHasKey(
                $fieldName,
                $fields,
                "Failed asserting that a field with the name [{$fieldName}] is hidden on the form named [{$formName}] on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertFormFieldIsVisible(): Closure
    {
        return function (string $fieldName, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormFieldExists($fieldName, $formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            $fields = $form->getFlatFields(withHidden: false);

            $livewireClass = $this->instance()::class;

            Assert::assertArrayHasKey(
                $fieldName,
                $fields,
                "Failed asserting that a field with the name [{$fieldName}] is visible on the form named [{$formName}] on the [{$livewireClass}] component."
            );

            return $this;
        };
    }

    public function assertWizardStepExists(): Closure
    {
        return function (int $step, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var Wizard $wizard */
            $wizard = $form->getComponent(fn (Component $component): bool => $component instanceof Wizard);
            Assert::assertArrayHasKey(
                $step - 1,
                $wizard->getChildComponents(),
                "Wizard does not have a step {$step}."
            );

            return $this;
        };
    }

    public function assertWizardCurrentStep(): Closure
    {
        return function (int $step, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var Wizard $wizard */
            $wizard = $form->getComponent(fn (Component $component): bool => $component instanceof Wizard);
            Assert::assertEquals(
                $step,
                $current = $wizard->getCurrentStepIndex() + 1,
                "Failed asserting that wizard is on step {$step}, current step is {$current}."
            );

            return $this;
        };
    }

    public function goToWizardStep(): Closure
    {
        return function (int $step, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertWizardStepExists($step, $formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            $stepIndex = $step <= 1 ? 0 : $step - 2;
            $this->call('dispatchFormEvent', 'wizard::nextStep', $form->getStatePath(), $stepIndex);

            return $this;
        };
    }

    public function goToNextWizardStep(): Closure
    {
        return function (string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var Wizard $wizardComponent */
            $wizardComponent = $form->getComponent(fn (Component $component): bool => $component instanceof Wizard);
            $nextStep = $wizardComponent->getCurrentStepIndex() + 2;

            /** @phpstan-ignore-next-line  */
            return $this->goToWizardStep($nextStep, $formName);
        };
    }

    public function goToPreviousWizardStep(): Closure
    {
        return function (string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            /** @var ComponentContainer $form */
            $form = $this->instance()->{$formName};

            /** @var Wizard $wizardComponent */
            $wizardComponent = $form->getComponent(fn (Component $component): bool => $component instanceof Wizard);
            $previousStepIndex = $wizardComponent->getCurrentStepIndex() - 1;

            $this->call('dispatchFormEvent', 'wizard::previousStep', $form->getStatePath(), $previousStepIndex);

            return $this;
        };
    }
}
