<?php

namespace Filament\Notifications\Actions;

use Filament\Notifications\Actions\Concerns\CanCloseNotification;
use Filament\Notifications\Actions\Concerns\CanEmitEvent;
use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Illuminate\Contracts\Support\Arrayable;

class Action extends BaseAction implements Arrayable
{
    use CanBeOutlined;
    use CanCloseNotification;
    use CanEmitEvent;
    use CanOpenUrl;

    protected string $view = 'notifications::actions.link-action';

    protected string $viewIdentifier = 'action';

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'color' => $this->getColor(),
            'event' => $this->getEvent(),
            'eventData' => $this->getEventData(),
            'extraAttributes' => $this->getExtraAttributes(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'isOutlined' => $this->isOutlined(),
            'isDisabled' => $this->isDisabled(),
            'label' => $this->getLabel(),
            'shouldCloseNotification' => $this->shouldCloseNotification(),
            'shouldOpenUrlInNewTab' => $this->shouldOpenUrlInNewTab(),
            'size' => $this->getSize(),
            'url' => $this->getUrl(),
        ];
    }

    public static function fromArray($value): static
    {
        $static = static::make($value['name']);
        $static->close($value['shouldCloseNotification']);
        $static->color($value['color']);
        $static->disabled($value['isDisabled']);
        $static->emit($value['event'], $value['eventData']);
        $static->extraAttributes($value['extraAttributes']);
        $static->icon($value['icon']);
        $static->iconPosition($value['iconPosition']);
        $static->label($value['label']);
        $static->outlined($value['isOutlined']);
        $static->size($value['size']);
        $static->url($value['url'], $value['shouldOpenUrlInNewTab']);

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
