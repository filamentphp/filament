<?php

namespace Filament\Forms\Testing;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasForms instance()
 *
 * @mixin TestableLivewire
 */
class TestsForms
{
    public function fillForm(): Closure
    {
        return function (array $state = [], string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $formStatePath = $form->getStatePath();

            foreach ($state as $key => $value) {
                $this->set((filled($formStatePath) ? "{$formStatePath}.{$key}" : $key), $value);
            }

            return $this;
        };
    }

    public function assertFormSet(): Closure
    {
        return function (array $state, string $formName = 'form'): static {
            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $formStatePath = $form->getStatePath();

            foreach ($state as $key => $value) {
                $this->assertSet((filled($formStatePath) ? "{$formStatePath}.{$key}" : $key), $value);
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

    public function assertFormFieldExists(): Closure
    {
        return function (string $fieldName, string $formName = 'form'): static {
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

            Assert::assertTrue(
                $field->isDisabled(),
                "Failed asserting that a field with the name [{$fieldName}] is disabled on the form named [{$formName}] on the [{$livewireClass}] component."
            );

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

            Assert::assertFalse(
                $field->isDisabled(),
                "Failed asserting that a field with the name [{$fieldName}] is enabled on the form named [{$formName}] on the [{$livewireClass}] component."
            );

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
}
