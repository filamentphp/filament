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

    public function getIcon(): string | BackedEnum | Htmlable
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
        $icon = $this->getIcon();

        $tooltip = $this->getTooltip();
        $hasTooltip = filled($tooltip);

        $extraAttributes = $this->getExtraAttributes();

        $ariaLabel = $extraAttributes['aria-label'] ?? null;
        $ariaLabelledBy = $extraAttributes['aria-labelledby'] ?? null;
        $ariaDescribedBy = $extraAttributes['aria-describedby'] ?? null;

        $normalizeText = static fn (mixed $text): mixed => $text instanceof Htmlable
            ? trim(strip_tags($text->toHtml()))
            : $text;

        $ariaLabel = $normalizeText($ariaLabel);

        $isImagePathIcon = is_string($icon) && str_contains($icon, '/');

        if ($isImagePathIcon && blank($ariaLabel) && blank($ariaLabelledBy) && blank($extraAttributes['alt'] ?? null) && $hasTooltip) {
            $ariaLabel = $normalizeText($tooltip);
            $extraAttributes['aria-label'] = $ariaLabel;
        }

        foreach (['alt', 'aria-describedby', 'aria-label', 'aria-labelledby'] as $attribute) {
            if (filled($extraAttributes[$attribute] ?? null)) {
                $extraAttributes[$attribute] = e($normalizeText($extraAttributes[$attribute]), doubleEncode: false);
            }
        }

        if (! $isImagePathIcon) {
            $extraAttributes['aria-hidden'] = 'true';
        }

        $iconAttributes = [
            'x-tooltip' => $hasTooltip ? '{ content: ' . Js::from($tooltip) . ', theme: $store.theme, allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ' }' : null,
        ];

        $html = generate_icon_html($icon, attributes: (new FilamentComponentAttributeBag($iconAttributes))->merge($extraAttributes, escape: false)->color(IconComponent::class, $this->getColor() ?? 'primary')->class([
            'fi-sc-icon',
            'fi-sc-icon-htmlable' => $icon instanceof Htmlable,
        ]), size: $size instanceof IconSize ? $size : null)?->toHtml() ?? '';

        if ($isImagePathIcon) {
            return $html;
        }

        if (blank($ariaLabel) && blank($ariaLabelledBy) && $hasTooltip) {
            $ariaLabel = $normalizeText($tooltip);
        }

        if (blank($ariaLabel) && blank($ariaLabelledBy)) {
            return $html;
        }

        $accessibleAttributes = (new FilamentComponentAttributeBag([
            'aria-describedby' => filled($ariaDescribedBy) ? e($normalizeText($ariaDescribedBy), doubleEncode: false) : null,
            'aria-label' => filled($ariaLabel) ? e($ariaLabel, doubleEncode: false) : null,
            'aria-labelledby' => filled($ariaLabelledBy) ? e($normalizeText($ariaLabelledBy), doubleEncode: false) : null,
            'role' => 'img',
        ]))->class(['fi-sr-only']);

        return $html . '<span ' . $accessibleAttributes->toHtml() . '></span>';
    }
}
