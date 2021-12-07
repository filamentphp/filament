<?php

namespace Filament\Forms\Components\Concerns;

trait CanSpanColumns
{
    protected array $columnSpan = [
        'default' => 1,
        'sm' => null,
        'md' => null,
        'lg' => null,
        'xl' => null,
        '2xl' => null,
    ];

    public function columnSpan(array | int | string | null $span): static
    {
        if (! is_array($span)) {
            $span = [
                'default' => $span,
            ];
        }

        $this->columnSpan = array_merge($this->columnSpan, $span);

        return $this;
    }

    public function getColumnSpan($breakpoint = null): array | int | string | null
    {
        $span = $this->columnSpan;

        if ($breakpoint !== null) {
            $span = $span[$breakpoint] ?? null;
        }

        return $span;
    }
}
