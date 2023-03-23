<?php

namespace Filament\Notifications\Actions;

use Filament\Notifications\Actions\Concerns\CanCloseNotification;
use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanEmitEvent;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

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
            'emitDirection' => $this->getEmitDirection(),
            'emitToComponent' => $this->getEmitToComponent(),
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
            'view' => $this->getView(),
        ];
    }

    public static function fromArray(array $data): static
    {
        $static = static::make($data['name']);

        $view = $data['view'] ?? null;

        if (filled($view) && ($static->getView() !== $view) && static::isViewSafe($view)) {
            $static->view($data['view']);
        }

        $static->close($data['shouldCloseNotification'] ?? false);
        $static->color($data['color'] ?? null);
        $static->disabled($data['isDisabled'] ?? false);

        match ($data['emitDirection'] ?? null) {
            'self' => $static->emitSelf($data['event'] ?? null, $data['eventData'] ?? []),
            'up' => $static->emitUp($data['event'] ?? null, $data['eventData'] ?? []),
            'to' => $static->emitTo($data['emitToComponent'] ?? null, $data['event'] ?? null, $data['eventData'] ?? []),
            default => $static->emit($data['event'] ?? null, $data['eventData'] ?? [])
        };

        $static->extraAttributes($data['extraAttributes'] ?? []);
        $static->icon($data['icon'] ?? null);
        $static->iconPosition($data['iconPosition'] ?? null);
        $static->label($data['label'] ?? null);
        $static->outlined($data['isOutlined'] ?? false);
        $static->size($data['size'] ?? null);
        $static->url($data['url'] ?? null, $data['shouldOpenUrlInNewTab'] ?? false);

        return $static;
    }

    protected static function isViewSafe(string $view): bool
    {
        return Str::startsWith($view, 'notifications::actions.');
    }

    public function button(): static
    {
        $this->view('notifications::actions.button-action');

        return $this;
    }

    public function grouped(): static
    {
        $this->view('notifications::actions.grouped-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('notifications::actions.link-action');

        return $this;
    }
}
