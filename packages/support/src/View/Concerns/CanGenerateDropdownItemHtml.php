<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Enums\IconSize;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\DropdownComponent\ItemComponent;
use Filament\Support\View\Components\DropdownComponent\ItemComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanGenerateDropdownItemHtml
{
    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     *
     * @param  string | array<string> | null  $badgeColor,
     * @param  string | array<string> | null  $color,
     * @param  array<string>  $keyBindings
     */
    public function generateDropdownItemHtml(
        ComponentAttributeBag $attributes,
        string | Htmlable | null $badge = null,
        string | array | null $badgeColor = null,
        ?string $badgeTooltip = null,
        string | array | null $color = 'primary',
        bool $hasLoadingIndicator = true,
        ?bool $hasSpaMode = null,
        ?string $href = null,
        string | BackedEnum | Htmlable | null $icon = null,
        ?string $iconAlias = null,
        ?string $iconColor = null,
        IconSize | string | null $iconSize = null,
        bool $isDisabled = false,
        ?array $keyBindings = null,
        string | Htmlable | null $label = null,
        string $tag = 'button',
        ?string $target = null,
        ?string $tooltip = null,
        ?string $type = 'button',
    ): string {
        $color ??= 'gray';

        if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
            $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
        }

        $iconColor ??= $color;

        $wireTarget = $hasLoadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

        $hasLoadingIndicator = filled($wireTarget);

        if ($hasLoadingIndicator) {
            $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
        }

        $hasTooltip = filled($tooltip);

        $formAttributes = $attributes->only(['action', 'method', 'wire:submit']);

        $attributes = $attributes
            ->when(
                $tag === 'form',
                fn (ComponentAttributeBag $attributes) => $attributes->except(['action', 'class', 'method', 'wire:submit']),
            )
            ->merge([
                'aria-disabled' => $isDisabled ? 'true' : null,
                'disabled' => $isDisabled && blank($tooltip),
                'type' => match ($tag) {
                    'button' => $type,
                    'form' => 'submit',
                    default => null,
                },
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->when(
                $isDisabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'fi-dropdown-list-item',
                'fi-disabled' => $isDisabled,
            ])
            ->color(ItemComponent::class, $color);

        ob_start(); ?>

        <?= ($tag === 'form') ? ('<form ' . $formAttributes->toHtml() . '>' . csrf_field()) : '' ?>

        <<?= ($tag === 'form') ? 'button' : $tag ?>
            <?php if (($tag === 'a') && (! ($isDisabled && $hasTooltip))) { ?>
                <?= generate_href_html($href, $target === '_blank', $hasSpaMode)->toHtml() ?>
            <?php } ?>
            <?php if ($keyBindings) { ?>
                x-bind:id="$id('key-bindings')"
                x-mousetrap.global.<?= collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') ?>="document.getElementById($el.id).click()"
            <?php } ?>
            <?php if ($hasTooltip) { ?>
                x-tooltip="{
                    content: <?= Js::from($tooltip) ?>,
                    theme: $store.theme,
                }"
            <?php } ?>
            <?= $attributes->toHtml() ?>
        >
            <?= $icon ? generate_icon_html($icon, $iconAlias, (new ComponentAttributeBag([
                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
            ]))->color(IconComponent::class, $iconColor), size: $iconSize)->toHtml() : '' ?>
            <?= $hasLoadingIndicator ? generate_loading_indicator_html((new ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ])), size: $iconSize)->toHtml() : '' ?>

            <span class="fi-dropdown-list-item-label">
                <?= e($label) ?>
            </span>

            <?php if (filled($badge)) { ?>
                <span
                    <?php if (filled($badgeTooltip)) { ?>
                        x-tooltip="{
                            content: <?= Js::from($badgeTooltip) ?>,
                            theme: $store.theme,
                        }"
                    <?php } ?>
                    <?= (new ComponentAttributeBag)->color(BadgeComponent::class, $badgeColor)->class(['fi-badge'])->toHtml() ?>
                >
                    <?= e($badge) ?>
                </span>
            <?php } ?>
        </<?= ($tag === 'form') ? 'button' : $tag ?>>

        <?= ($tag === 'form') ? '</form>' : '' ?>

        <?php return ob_get_clean();
    }
}
