<?php

namespace Filament\Schemas\Components;

use BackedEnum;
use Closure;
use Filament\Schemas\View\Components\IconComponent;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasTooltip;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class Icon extends Component implements HasEmbeddedView
{
    use HasColor;
    use HasTooltip;

    protected string | BackedEnum | Htmlable | Closure $icon;

    final public function __construct(string | BackedEnum | Htmlable | Closure $icon)
    {
        $this->icon($icon);
    }

    public static function make(string | BackedEnum | Htmlable | Closure $icon): static
    {
        $static = app(static::class, ['icon' => $icon]);
        $static->configure();

        return $static;
    }

    public function icon(string | BackedEnum | Htmlable | Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string | BackedEnum
    {
        return $this->evaluate($this->icon);
    }

    public function toEmbeddedHtml(): string
    {
        return generate_icon_html($this->getIcon(), attributes: (new ComponentAttributeBag([
            'x-tooltip' => filled($tooltip = $this->getTooltip()) ? '{ content: ' . Js::from($tooltip) . ', theme: $store.theme, allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ' }' : null,
        ]))->merge($this->getExtraAttributes(), escape: false)->color(IconComponent::class, $this->getColor() ?? 'primary')->class(['fi-sc-icon']))->toHtml();
    }
}
