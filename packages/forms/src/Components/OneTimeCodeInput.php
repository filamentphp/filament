<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Support\Js;

class OneTimeCodeInput extends Field implements HasEmbeddedView
{
    use Concerns\CanBeReadOnly;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.one-time-code-input';

    protected int | Closure $length = 6;

    protected bool | Closure $shouldSubmitOnCompletion = false;

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

    public function submitOnCompletion(bool | Closure $condition = true): static
    {
        $this->shouldSubmitOnCompletion = $condition;

        return $this;
    }

    public function shouldSubmitOnCompletion(): bool
    {
        return (bool) $this->evaluate($this->shouldSubmitOnCompletion);
    }

    public function toEmbeddedHtml(): string
    {
        $length = $this->getLength();

        $entangle = $this->applyStateBindingModifiers("\$entangle('{$this->getStatePath()}')");

        $containerAttributes = $this->getExtraAttributeBag()
            ->merge($this->getExtraAlpineAttributes(), escape: false)
            ->class(['fi-one-time-code-input-ctn']);

        // The first input carries the `one-time-code` autocomplete so that browser and OS
        // autofill targets it. The component then distributes the filled value across the
        // remaining inputs.
        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'autofocus' => $this->isAutofocused(),
                'disabled' => $this->isDisabled(),
                'id' => $this->getId(),
                'readonly' => $this->isReadOnly(),
                'required' => $this->isRequired(),
            ], escape: false);

        // The remaining inputs mirror the first input's resolved `disabled` and `readonly`
        // state so that an `extraInputAttributes()` override applies to every digit.
        $isDisabled = filter_var($inputAttributes->get('disabled'), FILTER_VALIDATE_BOOLEAN);
        $isReadOnly = filter_var($inputAttributes->get('readonly'), FILTER_VALIDATE_BOOLEAN);

        ob_start(); ?>

        <div x-data="{ code: $wire.<?= $entangle ?>, }">
            <div
                x-data="filamentOneTimeCodeInput({ shouldSubmitOnCompletion: <?= Js::from($this->shouldSubmitOnCompletion()) ?> })"
                x-modelable="state"
                x-model="code"
                dir="ltr"
                role="group"
                <?= $containerAttributes->toHtml() ?>
            >
                <?php foreach (range(0, $length - 1) as $index) { ?>
                    <?php if ($index === 0) { ?>
                        <input
                            autocomplete="one-time-code"
                            inputmode="numeric"
                            type="text"
                            <?= $inputAttributes->class(['fi-one-time-code-input-digit'])->toHtml() ?>
                        />
                    <?php } else { ?>
                        <input
                            aria-label="<?= e(__('filament::components/input/one-time-code.aria_label', ['position' => $index + 1, 'count' => $length])) ?>"
                            autocomplete="off"
                            inputmode="numeric"
                            type="text"
                            <?php if ($isDisabled) { ?> disabled <?php } ?>
                            <?php if ($isReadOnly) { ?> readonly <?php } ?>
                            class="fi-one-time-code-input-digit"
                        />
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
