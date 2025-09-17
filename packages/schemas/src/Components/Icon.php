<?php

namespace Filament\Schemas\Components;

use BackedEnum;
use Closure;
use Filament\Schemas\View\Components\IconComponent;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasTooltip;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class Icon extends Component implements HasEmbeddedView
{
    use HasColor;
    use HasTooltip;

    protected string | BackedEnum | Closure $icon;

    final public function __construct(string | BackedEnum | Closure $icon)
    {
        $this->icon($icon);
    }

    public static function make(string | BackedEnum | Closure $icon): static
    {
        $static = app(static::class, ['icon' => $icon]);
        $static->configure();

        return $static;
    }

    public function icon(string | BackedEnum | Closure $icon): static
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
            'x-tooltip' => filled($tooltip = $this->getTooltip()) ? '{ content: ' . Js::from($tooltip) . ', theme: $store.theme }' : null,
        ]))->merge($this->getExtraAttributes(), escape: false)->color(IconComponent::class, $this->getColor() ?? 'primary')->class(['fi-sc-icon']))->toHtml();
    }
}
