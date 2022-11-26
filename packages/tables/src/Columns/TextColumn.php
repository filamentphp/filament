<?php

namespace Filament\Tables\Columns;

use BackedEnum;
use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;
use stdClass;

class TextColumn extends Column
{
    use Concerns\CanBeCopied;
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasDescription;
    use Concerns\HasFontFamily;
    use Concerns\HasIcon;
    use Concerns\HasSize;
    use Concerns\HasWeight;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.text-column';

    protected bool | Closure $canWrap = false;

    protected ?string $enum = null;

    public function enum(string | array | Arrayable $enum, $default = null): static
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

    public function rowIndex(bool $isFromZero = false): static
    {
        $this->getStateUsing(static function (stdClass $rowLoop) use ($isFromZero): string {
            return (string) $rowLoop->{$isFromZero ? 'index' : 'iteration'};
        });

        return $this;
    }

    public function wrap(bool | Closure $condition = true): static
    {
        $this->canWrap = $condition;

        return $this;
    }

    public function canWrap(): bool
    {
        return $this->evaluate($this->canWrap);
    }

    protected function mutateArrayState(array $state): string
    {
        return implode(', ', $state);
    }
}
