<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait CanFormatArrayState
{
    protected ?int $limit = null;

    protected ?Closure $formatArrayStateUsing = null;

    public function formatArrayStateUsing(?Closure $callback): static
    {
        $this->formatArrayStateUsing = $callback;

        return $this;
    }

    public function ul(): static
    {
        $this->formatArrayStateUsing(function ($state) {
            if (blank($state)) {
                return null;
            }

            if (!is_array($state)) {
                $state = [$state];
            }

            return new HtmlString(
                '<ul><li>' . implode('</li><li>', $state) . '</li></ul>'
            );
        });

        return $this;
    }

    public function ol(): static
    {
        $this->formatArrayStateUsing(function ($state) {
            if (blank($state)) {
                return null;
            }

            if (!is_array($state)) {
                $state = [$state];
            }

            return new HtmlString(
                '<ol><li>' . implode('</li><li>', $state) . '</li></ol>'
            );
        });

        return $this;
    }

    public function grid(int $columns = 1, $gap = 2): static
    {
        $this->formatArrayStateUsing(function ($state) use ($columns, $gap) {
            if (blank($state)) {
                return null;
            }

            if (!is_array($state)) {
                $state = [$state];
            }

            return new HtmlString(
                "<div class='grid grid-cols-{$columns} gap-{$gap}'><div>" .
                implode('</div><div>', $state) .
                '</div></div>'
            );
        });

        return $this;
    }

    public function getFormattedArrayState()
    {
        $state = $this->evaluate($this->formatArrayStateUsing ?? fn ($state) => $state);

        return $state;
    }
}
