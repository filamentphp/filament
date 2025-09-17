<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Illuminate\Contracts\Support\Htmlable;

trait HasHelperText
{
    public function helperText(string | Htmlable | Closure | null $text): static
    {
        $this->belowContent(function (Component $component) use ($text): ?Text {
            $content = $component->evaluate($text);

            if (blank($content)) {
                return null;
            }

            return Text::make($content);
        });

        return $this;
    }
}
