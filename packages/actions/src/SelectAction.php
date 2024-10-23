<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\HasId;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\View\ComponentAttributeBag;

class SelectAction extends Action implements HasEmbeddedView
{
    use HasId;

    /**
     * @var array<string> | Arrayable | string | Closure
     */
    protected array | Arrayable | string | Closure $options = [];

    protected ?string $placeholder = null;

    /**
     * @param  array<string> | Arrayable | string | Closure  $options
     */
    public function options(array | Arrayable | string | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getOptions(): array
    {
        $options = $this->evaluate($this->options) ?? [];

        if (
            is_string($options) &&
            enum_exists($enum = $options)
        ) {
            if (is_a($enum, LabelInterface::class, allow_string: true)) {
                return array_reduce($enum::cases(), function (array $carry, LabelInterface & UnitEnum $case): array {
                    $carry[$case?->value ?? $case->name] = $case->getLabel() ?? $case->name;

                    return $carry;
                }, []);
            }

            return array_reduce($enum::cases(), function (array $carry, UnitEnum $case): array {
                $carry[$case?->value ?? $case->name] = $case->name;

                return $carry;
            }, []);
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function toEmbeddedHtml(): string
    {
        $id = $this->getId();
        $isDisabled = $this->isDisabled();

        $inputWrapperAttributes = (new ComponentAttributeBag)
            ->class([
                'fi-input-wrp',
                'fi-disabled' => $isDisabled,
            ]);

        $inputAttributes = (new ComponentAttributeBag)
            ->merge([
                'disabled' => $isDisabled,
                'id' => $id,
                'wire:model.live' => $this->getName(),
            ], escape: false)
            ->class([
                'fi-select-input',
            ]);

        ob_start(); ?>

        <div class="fi-ac-select-action">
            <label for="<?= $id ?>" class="sr-only">
                <?= e($this->getLabel()) ?>
            </label>

            <div <?= $inputWrapperAttributes->toHtml() ?>>
                <select <?= $inputAttributes->toHtml() ?>>
                    <?php if (($placeholder = $this->getPlaceholder()) !== null) { ?>
                        <option value=""><?= e($placeholder) ?></option>
                    <?php } ?>

                    <?php foreach ($this->getOptions() as $value => $label) { ?>
                        <option value="<?= e($value) ?>">
                            <?= e($label) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
