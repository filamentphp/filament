<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\View\ComponentAttributeBag;

class OneTimeCodeInput extends Field implements HasEmbeddedView
{
    use Concerns\CanBeReadOnly;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected int | Closure $length = 6;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule('numeric'); // Integer validation does not allow leading zeros.
        $this->rule(static fn (OneTimeCodeInput $component): string => "digits:{$component->getLength()}");
    }

    public function length(int | Closure $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getLength(): int
    {
        return $this->evaluate($this->length);
    }

    public function toEmbeddedHtml(): string
    {
        $placeholder = $this->getPlaceholder();
        $length = $this->getLength();

        $outerAttributes = \Filament\Support\prepare_inherited_attributes(
            $this->getExtraAttributeBag()
                ->merge($this->getExtraAlpineAttributes(), escape: false)
        )->class(['fi-one-time-code-input-ctn']);

        $inputAttributes = \Filament\Support\prepare_inherited_attributes(
            $this->getExtraInputAttributeBag()
                ->merge([
                    'autocomplete' => false,
                    'autofocus' => $this->isAutofocused(),
                    'disabled' => $this->isDisabled(),
                    'id' => $this->getId(),
                    'length' => $length,
                    'placeholder' => filled($placeholder) ? e($placeholder) : null,
                    'readonly' => $this->isReadOnly(),
                    'required' => $this->isRequired() && (! $this->isConcealed()),
                    $this->applyStateBindingModifiers('wire:model') => $this->getStatePath(),
                ], escape: false)
        );

        ob_start(); ?>

        <div
            x-data="{ currentNumberOfDigits: null }"
            <?= $outerAttributes->toHtml() ?>
        >
            <?php foreach (range(1, $length) as $digit) { ?>
                <div
                    x-bind:class="{
                        'fi-active':
                            currentNumberOfDigits !== null &&
                            currentNumberOfDigits >= <?= $digit ?>,
                    }"
                    class="fi-one-time-code-input-digit-field"
                ></div>
            <?php } ?>

            <input
                autocomplete="one-time-code"
                inputmode="numeric"
                minlength="<?= $length ?>"
                maxlength="<?= $length ?>"
                pattern="\d<?= '{' . $length . '}' ?>"
                type="text"
                x-data="{}"
                x-on:focus="currentNumberOfDigits = $el.value.length"
                x-on:blur="currentNumberOfDigits = null"
                x-on:input="
                    $el.value = $el.value.replace(/\D/g, '')
                    currentNumberOfDigits = $el.value.length
                "
                x-bind:class="{ 'fi-valid': currentNumberOfDigits >= <?= $length ?> }"
                <?= $inputAttributes->toHtml() ?>
                class="fi-one-time-code-input"
            />
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
