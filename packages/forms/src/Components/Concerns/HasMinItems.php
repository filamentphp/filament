<?php

namespace Filament\Forms\Components\Concerns;

trait HasMinItems
{
    protected $minItems = null;

    public function minItems(int | callable $count): static
    {
        $this->minItems = $count;

        $this->rule(function (): callable {
            $minItems = $this->getMinItems();
            $label = $this->getValidationAttribute();

            return function($attribute, $value, $fail) use ($minItems, $label) {
                if (count(array_filter($value, fn($item) => !array_filter(array_map('array_filter', $item)))) < $minItems) {
                    $fail(trans('validation.min.array', [
                        'attribute' => $label,
                        'min' => $minItems
                    ]));
                }
            } ;
        });

        return $this;
    }

    public function getMinItems(): ?int
    {
        return $this->evaluate($this->minItems);
    }

    public function reachedMinItems(int | callable $count): bool
    {
        return $this->getMinItems() !== null && $this->evaluate($this->count) > $this->getMinItems();
    }
}
