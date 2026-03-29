<?php

namespace Filament\Actions;

use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;

class ActionSeparator extends ViewComponent implements HasEmbeddedView
{
    use Concerns\BelongsToGroup;
    use Concerns\CanBeHidden;

    protected string $evaluationIdentifier = 'separator';

    protected string $viewIdentifier = 'separator';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function isHiddenInGroup(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        if (! $this->evaluate($this->isVisible)) {
            return true;
        }

        return false;
    }

    public function toEmbeddedHtml(): string
    {
        if ($this->isHidden()) {
            return '';
        }

        return '<div class="fi-ac-separator"></div>';
    }
}
