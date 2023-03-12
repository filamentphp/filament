<?php

namespace Filament\GlobalSearch\Actions;

use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\CanEmitEvent;
use Filament\Actions\Concerns\CanOpenUrl;
use Filament\Actions\Concerns\HasKeyBindings;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\StaticAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;

class Action extends StaticAction
{
    use CanBeOutlined;
    use CanEmitEvent;
    use CanOpenUrl;
    use HasKeyBindings;
    use HasTooltip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::LINK_VIEW);

        $this->defaultSize('sm');
    }

    public function getLivewireMountAction(): ?string
    {
        if ($this->getUrl()) {
            return null;
        }

        $event = $this->getEvent();

        if (! $event) {
            return null;
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

    public function getAlpineMountAction(): ?string
    {
        return null;
    }
}
