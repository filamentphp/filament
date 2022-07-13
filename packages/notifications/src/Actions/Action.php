<?php

namespace Filament\Notifications\Actions;

use Closure;
use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Livewire\Wireable;

class Action extends BaseAction implements Wireable
{
    use CanBeOutlined;
    use CanOpenUrl;

    protected string $view = 'notifications::actions.link-action';

    protected string $viewIdentifier = 'action';

    protected string | Closure | null $event = null;

    protected bool $shouldCloseNotification = false;

    public function toLivewire(): array
    {
        return [
            'view' => $this->getView(),
            'isDisabled' => $this->isDisabled(),
            'isHidden' => $this->isHidden(),
            'color' => $this->getColor(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'label' => $this->getLabel(),
            'name' => $this->getName(),
            'size' => $this->getSize(),
            'extraAttributes' => $this->getExtraAttributes(),
            'shouldOpenUrlInNewTab' => $this->shouldOpenUrlInNewTab(),
            'url' => $this->getUrl(),
            'isOutlined' => $this->isOutlined(),
            'event' => $this->getEvent(),
            'shouldCloseNotification' => $this->shouldCloseNotification(),
        ];
    }

    public static function fromLivewire($value): static
    {
        $static = static::make($value['name']);

        foreach ($value as $key => $value) {
            $static->{$key} = $value;
        }

        return $static;
    }

    public function button(): static
    {
        $this->view('notifications::actions.button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('notifications::actions.link-action');

        return $this;
    }

    public function event(string | Closure | null $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->evaluate($this->event);
    }

    public function closeNotification(bool $condition = true): static
    {
        $this->shouldCloseNotification = $condition;

        return $this;
    }

    public function shouldCloseNotification(): bool
    {
        return $this->shouldCloseNotification;
    }
}
