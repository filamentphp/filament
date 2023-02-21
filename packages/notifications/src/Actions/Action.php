<?php

namespace Filament\Notifications\Actions;

use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\CanEmitEvent;
use Filament\Actions\Concerns\CanOpenUrl;
use Filament\Actions\Concerns\HasKeyBindings;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\Contracts\Groupable;
use Filament\Actions\StaticAction;
use Filament\Notifications\Actions\Concerns\CanCloseNotification;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class Action extends StaticAction implements Arrayable, Groupable
{
    use CanBeOutlined;
    use CanCloseNotification;
    use CanEmitEvent;
    use CanOpenUrl;
    use HasKeyBindings;
    use HasTooltip;

    /**
     * @var view-string
     */
    protected string $view = 'filament-actions::link-action';

    protected string $viewIdentifier = 'action';

    protected function setUp(): void
    {
        parent::setUp();

        $this->size('sm');
    }

    /**
     * @return array<string, mixed>
     */
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
            'iconSize' => $this->getIconSize(),
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

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $static = static::make($data['name']);

        $view = $data['view'] ?? null;

        if (filled($view) && ($static->getView() !== $view) && static::isViewSafe($view)) {
            $static->view($view);
        }

        if (filled($size = $data['size'] ?? null)) {
            $static->size($size);
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
        $static->iconSize($data['iconSize'] ?? null);
        $static->label($data['label'] ?? null);
        $static->outlined($data['isOutlined'] ?? false);
        $static->url($data['url'] ?? null, $data['shouldOpenUrlInNewTab'] ?? false);

        return $static;
    }

    /**
     * @param  view-string  $view
     */
    protected static function isViewSafe(string $view): bool
    {
        return Str::startsWith($view, 'filament-actions::');
    }

    public function getLivewireMountAction(): ?string
    {
        if ($this->shouldCloseNotification()) {
            return null;
        }

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
                $this->emitToComponent,
                fn (Collection $collection, string $component) => $collection->prepend($component),
            )
            ->map(fn (mixed $value): string => Js::from($value)->toHtml())
            ->implode(', ');

        return match ($this->emitDirection) {
            'self' => "\$emitSelf($arguments)",
            'to' => "\$emitTo($arguments)",
            'up' => "\$emitUp($arguments)",
            default => "\$emit($arguments)"
        };
    }

    public function getAlpineMountAction(): ?string
    {
        if (! $this->shouldCloseNotification()) {
            return null;
        }

        return 'close()';
    }
}
