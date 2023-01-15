<?php

namespace Filament\Infolists\Components;

use BackedEnum;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;

class TextEntry extends Entry
{
    use Concerns\CanBeCopied;
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasFontFamily;
    use Concerns\HasIcon;
    use Concerns\HasSize;
    use Concerns\HasWeight;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.text-entry';

    protected ?string $enum = null;

    /**
     * @param  string | array<scalar, scalar> | Arrayable  $enum
     */
    public function enum(string | array | Arrayable $enum, mixed $default = null): static
    {
        if (is_array($enum) || $enum instanceof Arrayable) {
            $this->formatStateUsing(static fn ($state): ?string => $enum[$state] ?? ($default ?? $state));

            return $this;
        }

        $this->enum = $enum;

        if (
            is_string($enum) &&
            function_exists('enum_exists') &&
            enum_exists($enum) &&
            is_a($enum, BackedEnum::class, allow_string: true) &&
            is_a($enum, LabelInterface::class, allow_string: true)
        ) {
            $this->formatStateUsing(static fn ($state): ?string => $enum::tryFrom($state)?->getLabel() ?? ($default ?? $state));
        }

        return $this;
    }

    /**
     * @param  array<scalar>  $state
     */
    protected function mutateArrayState(array $state): string
    {
        return implode(', ', $state);
    }
}
