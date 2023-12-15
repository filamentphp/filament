<?php

namespace Filament\Actions;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Support\Js;
use Illuminate\Support\Traits\Conditionable;

class StaticAction extends ViewComponent
{
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeLabeledFrom;
    use Concerns\CanBeOutlined;
    use Concerns\CanCallParentAction;
    use Concerns\CanClose;
    use Concerns\CanDispatchEvent;
    use Concerns\CanOpenUrl;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
    use Concerns\HasArguments;
    use Concerns\HasGroupedIcon;
    use Concerns\HasKeyBindings;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasSize;
    use Concerns\HasTooltip;
    use Conditionable;
    use HasBadge;
    use HasColor;
    use HasExtraAttributes;
    use HasIcon;

    public const BADGE_VIEW = 'filament-actions::badge-action';

    public const BUTTON_VIEW = 'filament-actions::button-action';

    public const GROUPED_VIEW = 'filament-actions::grouped-action';

    public const ICON_BUTTON_VIEW = 'filament-actions::icon-button-action';

    public const LINK_VIEW = 'filament-actions::link-action';

    protected string $evaluationIdentifier = 'action';

    protected string $viewIdentifier = 'action';

    protected ?string $livewireTarget = null;

    final public function __construct(?string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure();

        return $static;
    }

    public function isBadge(): bool
    {
        return $this->getView() === static::BADGE_VIEW;
    }

    public function button(): static
    {
        return $this->view(static::BUTTON_VIEW);
    }

    public function isButton(): bool
    {
        return $this->getView() === static::BUTTON_VIEW;
    }

    public function grouped(): static
    {
        return $this->view(static::GROUPED_VIEW);
    }

    public function iconButton(): static
    {
        return $this->view(static::ICON_BUTTON_VIEW);
    }

    public function isIconButton(): bool
    {
        return $this->getView() === static::ICON_BUTTON_VIEW;
    }

    public function link(): static
    {
        return $this->view(static::LINK_VIEW);
    }

    public function isLink(): bool
    {
        return $this->getView() === static::LINK_VIEW;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getLivewireClickHandler(): ?string
    {
        if (! $this->isLivewireClickHandlerEnabled()) {
            return null;
        }

        if (is_string($this->action)) {
            return $this->action;
        }

        if ($event = $this->getLivewireEventClickHandler()) {
            return $event;
        }

        if ($handler = $this->getParentActionCallLivewireClickHandler()) {
            $handler .= '(';
            $handler .= Js::from($this->getArguments());
            $handler .= ')';

            return $handler;
        }

        return null;
    }

    public function getLivewireEventClickHandler(): ?string
    {
        $event = $this->getEvent();

        if (blank($event)) {
            return null;
        }

        $arguments = '';

        if ($component = $this->getDispatchToComponent()) {
            $arguments .= Js::from($component)->toHtml();
            $arguments .= ', ';
        }

        $arguments .= Js::from($event)->toHtml();

        if ($this->getEventData()) {
            $arguments .= ', ';
            $arguments .= Js::from($this->getEventData())->toHtml();
        }

        return match ($this->getDispatchDirection()) {
            'self' => "\$dispatchSelf($arguments)",
            'to' => "\$dispatchTo($arguments)",
            default => "\$dispatch($arguments)"
        };
    }

    public function getAlpineClickHandler(): ?string
    {
        if (! $this->shouldClose()) {
            return null;
        }

        return 'close()';
    }

    public function livewireTarget(?string $target): static
    {
        $this->livewireTarget = $target;

        return $this;
    }

    public function getLivewireTarget(): ?string
    {
        return $this->livewireTarget;
    }

    /**
     * @deprecated Use `extraAttributes()` instead.
     *
     * @param  array<mixed>  $attributes
     */
    public function withAttributes(array $attributes): static
    {
        return $this->extraAttributes($attributes);
    }
}
