<?php

namespace Filament\Schemas\Components;

use BackedEnum;
use Closure;
use Filament\Schemas\View\Components\IconComponent;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Enums\IconSize;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class Icon extends Component implements HasEmbeddedView
{
    use HasColor;
    use HasTooltip;

    protected string | BackedEnum | Htmlable | Closure $icon;

    protected IconSize | string | Closure | null $size = null;

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

    public function size(IconSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): IconSize | string | null
    {
        $size = $this->evaluate($this->size);

        if (blank($size)) {
            return null;
        }

        if ($size === 'base') {
            return null;
        }

        if (is_string($size)) {
            $size = IconSize::tryFrom($size) ?? $size;
        }

        return $size;
    }

    public function toEmbeddedHtml(): string
    {
        $size = $this->getSize();

        $tooltip = $this->getTooltip();
        $hasTooltip = filled($tooltip);

        $extraAttributes = $this->getExtraAttributes();

        // A user-supplied `aria-label` via `extraAttributes()` would land on the icon's `<svg>`, but the
        // SVG carries a baked-in `aria-hidden="true"` (from the icon source) that removes it — and every
        // attribute on it, including the label — from the accessibility tree, leaving the icon unnamed.
        // Pull the label off the SVG and expose it as the icon's visually-hidden text alternative instead.
        $userLabel = $extraAttributes['aria-label'] ?? null;
        unset($extraAttributes['aria-label']);

        $iconAttributes = [
            'x-tooltip' => $hasTooltip ? '{ content: ' . Js::from($tooltip) . ', theme: $store.theme, allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ' }' : null,
        ];

        $html = generate_icon_html($this->getIcon(), attributes: (new FilamentComponentAttributeBag($iconAttributes))->merge($extraAttributes, escape: false)->color(IconComponent::class, $this->getColor() ?? 'primary')->class(['fi-sc-icon']), size: $size instanceof IconSize ? $size : null)?->toHtml() ?? '';

        // Give the decorative icon a visually-hidden text alternative. Priority: an explicit user
        // `aria-label`, then the tooltip (which is the icon's meaning but is hover-only). Skipped when the
        // user named the icon through `aria-labelledby` — they've deliberately pointed at another element.
        if (filled($userLabel)) {
            $accessibleText = $userLabel instanceof Htmlable ? trim(strip_tags($userLabel->toHtml())) : $userLabel;
        } elseif ($hasTooltip && blank($extraAttributes['aria-labelledby'] ?? null)) {
            $accessibleText = $tooltip instanceof Htmlable ? trim(strip_tags($tooltip->toHtml())) : $tooltip;
        } else {
            $accessibleText = null;
        }

        if (filled($accessibleText)) {
            $html .= '<span class="fi-sr-only">' . e($accessibleText) . '</span>';
        }

        return $html;
    }
}
