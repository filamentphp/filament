<?php

namespace Filament\GlobalSearch\Actions;

use Filament\Actions\Concerns\CanEmitEvent;
use Filament\Actions\StaticAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;

class Action extends StaticAction
{
    use CanEmitEvent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::LINK_VIEW);

        $this->defaultSize('sm');
    }

    public function getLivewireClickHandler(): ?string
    {
        if ($this->getUrl()) {
            return parent::getLivewireClickHandler();
        }

        $event = $this->getEvent();

        if (! $event) {
            return parent::getLivewireClickHandler();
        }

        $arguments = collect([$event])
            ->merge($this->getEventData())
            ->when(
                $this->getEmitToComponent(),
                fn (Collection $collection, string $component) => $collection->prepend($component),
            )
            ->map(fn (mixed $value): string => Js::from($value)->toHtml())
            ->implode(', ');

        return match ($this->getEmitDirection()) {
            'self' => "\$emitSelf($arguments)",
            'to' => "\$emitTo($arguments)",
            'up' => "\$emitUp($arguments)",
            default => "\$emit($arguments)"
        };
    }
}
