<?php

namespace Filament\Notifications\Actions;

use Filament\Notifications\Actions\Concerns\CanCloseNotification;
use Filament\Notifications\Actions\Concerns\HasEvent;
use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Illuminate\Contracts\Support\Arrayable;

class Action extends BaseAction implements Arrayable
{
    use CanBeOutlined;
    use CanCloseNotification;
    use CanOpenUrl;
    use HasEvent;

    protected string $view = 'notifications::actions.link-action';

    protected string $viewIdentifier = 'action';

    public function toArray(): array
    {
        return [
            'view' => $this->getView(),
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'color' => $this->getColor(),
            'size' => $this->getSize(),
            'isOutlined' => $this->isOutlined(),
            'isDisabled' => $this->isDisabled(),
            'isHidden' => $this->isHidden(),
            'url' => $this->getUrl(),
            'shouldOpenUrlInNewTab' => $this->shouldOpenUrlInNewTab(),
            'event' => $this->getEvent(),
            'eventData' => $this->getEventData(),
            'shouldCloseNotification' => $this->shouldCloseNotification(),
            'extraAttributes' => $this->getExtraAttributes(),
        ];
    }

    public static function fromArray($value): static
    {
        $static = static::make($value['name']);

        foreach ($value as $key => $value) {
            match ($key) {
                'name' => null,
                default => $static->{$key} = $value,
            };
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
}
