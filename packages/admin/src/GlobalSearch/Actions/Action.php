<?php

namespace Filament\GlobalSearch\Actions;

use Filament\GlobalSearch\Actions\Concerns\CanEmitEvent;
use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class Action extends BaseAction implements Arrayable
{
    use CanBeOutlined;
    use CanEmitEvent;
    use CanOpenUrl;

    protected string $view = 'filament::global-search.actions.link-action';

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
            'shouldOpenUrlInNewTab' => $this->shouldOpenUrlInNewTab(),
            'size' => $this->getSize(),
            'url' => $this->getUrl(),
            'view' => $this->getView(),
        ];
    }

    public static function fromArray(array $data): static
    {
        $static = static::make($data['name']);

        if ($static->getView() !== $data['view'] && static::isViewSafe($data['view'])) {
            $static->view($data['view']);
        }

        $static->color($data['color']);
        $static->disabled($data['isDisabled']);
        $static->emit($data['event'], $data['eventData']);
        $static->extraAttributes($data['extraAttributes']);
        $static->icon($data['icon']);
        $static->iconPosition($data['iconPosition']);
        $static->label($data['label']);
        $static->outlined($data['isOutlined']);
        $static->size($data['size']);
        $static->url($data['url'], $data['shouldOpenUrlInNewTab']);

        return $static;
    }

    protected static function isViewSafe(string $view): bool
    {
        return Str::startsWith($view, 'filament::global-search.actions.');
    }

    public function button(): static
    {
        $this->view('filament::global-search.actions.button-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament::global-search.actions.icon-button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament::global-search.actions.link-action');

        return $this;
    }
}
