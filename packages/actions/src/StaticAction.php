<?php

namespace Filament\Actions;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Support\Js;
use Illuminate\Support\Traits\Conditionable;

class StaticAction extends ViewComponent
{
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanCallParentAction;
    use Concerns\CanCancelParentAction;
    use Concerns\CanOpenUrl;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
    use Concerns\HasArguments;
    use Concerns\HasColor;
    use Concerns\HasGroupedIcon;
    use Concerns\HasIcon;
    use Concerns\HasIndicator;
    use Concerns\HasKeyBindings;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\CanBeOutlined;
    use Concerns\HasSize;
    use Concerns\HasTooltip;
    use Conditionable;
    use HasExtraAttributes;

    public const BUTTON_VIEW = 'filament-actions::button-action';

    public const GROUPED_VIEW = 'filament-actions::grouped-action';

    public const ICON_BUTTON_VIEW = 'filament-actions::icon-button-action';

    public const LINK_VIEW = 'filament-actions::link-action';

    protected string $evaluationIdentifier = 'action';

    protected string $viewIdentifier = 'action';

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

    public function button(): static
    {
        $this->view(static::BUTTON_VIEW);

        return $this;
    }

    public function grouped(): static
    {
        $this->view(static::GROUPED_VIEW);

        return $this;
    }

    public function iconButton(): static
    {
        $this->view(static::ICON_BUTTON_VIEW);

        return $this;
    }

    public function link(): static
    {
        $this->view(static::LINK_VIEW);

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getLivewireClickHandler(): ?string
    {
        if (is_string($this->action)) {
            return $this->action;
        }

        if ($handler = $this->getParentActionCallLivewireClickHandler()) {
            $handler .= '(';
            $handler .= Js::from($this->getArguments());
            $handler .= ')';

            return $handler;
        }

        return null;
    }

    public function getAlpineClickHandler(): ?string
    {
        if (! $this->canCancelParentAction()) {
            return null;
        }

        return 'close()';
    }

    /**
     * @deprecated Use `->extraAttributes()` instead.
     *
     * @param  array<mixed>  $attributes
     */
    public function withAttributes(array $attributes): static
    {
        return $this->extraAttributes($attributes);
    }
}
