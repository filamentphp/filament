<?php

namespace Filament\GlobalSearch\Actions;

use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\CanEmitEvent;
use Filament\Actions\Concerns\CanOpenUrl;
use Filament\Actions\Concerns\HasKeyBindings;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\StaticAction;
use Illuminate\Support\Js;

class Action extends StaticAction
{
    use CanBeOutlined;
    use CanEmitEvent;
    use CanOpenUrl;
    use HasKeyBindings;
    use HasTooltip;

    protected string $view = 'filament-actions::link-action';

    protected function setUp(): void
    {
        parent::setUp();

        $this->size('sm');
    }

    public function button(): static
    {
        $this->view('filament-actions::button-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament-actions::icon-button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament-actions::link-action');

        return $this;
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

        $emitArguments = collect([$event])
            ->merge($this->getEventData())
            ->map(fn ($value): string => Js::from($value)->toHtml())
            ->implode(', ');

        return "\$emit({$emitArguments})";
    }

    public function getAlpineMountAction(): ?string
    {
        return null;
    }
}
