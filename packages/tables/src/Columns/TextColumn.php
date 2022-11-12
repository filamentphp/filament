<?php

namespace Filament\Tables\Columns;

use Closure;
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

    protected string $view = 'tables::columns.text-column';

    protected bool | Closure $canWrap = false;

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
