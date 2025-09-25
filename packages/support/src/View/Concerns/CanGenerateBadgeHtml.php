<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\View\Components\BadgeComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanGenerateBadgeHtml
{
    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     *
     * @param  string | array<string> | null  $color
     * @param  array<string>  $keyBindings
     */
    public function generateBadgeHtml(
        ComponentAttributeBag $attributes,
        string | array | null $color = null,
        ?string $form = null,
        ?string $formId = null,
        bool $hasLoadingIndicator = true,
        ?bool $hasSpaMode = null,
        ?string $href = null,
        string | BackedEnum | Htmlable | null $icon = null,
        ?string $iconAlias = null,
        ?IconPosition $iconPosition = IconPosition::Before,
        IconSize | string | null $iconSize = null,
        bool $isDisabled = false,
        ?array $keyBindings = null,
        string | Htmlable | null $label = null,
        Size | string | null $size = null,
        string $tag = 'span',
        ?string $target = null,
        ?string $tooltip = null,
        ?string $type = 'button',
    ): string {
        $color ??= 'primary';

        if (! $iconPosition instanceof IconPosition) {
            $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
        }

        if (! $size instanceof Size) {
            $size = filled($size) ? (Size::tryFrom($size) ?? $size) : null;
        }

        if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
            $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
        }

        $wireTarget = $hasLoadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

        $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

        if ($hasLoadingIndicator) {
            $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
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
                'form' => $tag === 'button' ? $formId : null,
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
                'fi-badge',
                'fi-disabled' => $isDisabled,
                ($size instanceof Size) ? "fi-size-{$size->value}" : $size,
            ])
            ->color(BadgeComponent::class, $color);

        $iconHtml = $icon ? generate_icon_html($icon, $iconAlias, (new ComponentAttributeBag([
            'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
        ])), size: $iconSize ?? IconSize::Small)->toHtml() : '';

        $loadingIndicatorHtml = $hasLoadingIndicator ? generate_loading_indicator_html((new ComponentAttributeBag([
            'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
            'wire:target' => $loadingIndicatorTarget,
        ])), size: $iconSize ?? IconSize::Small)->toHtml() : '';

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
            <?php if ($iconPosition === IconPosition::Before) { ?>
                <?= $iconHtml ?>
                <?= $loadingIndicatorHtml ?>
            <?php } ?>

            <span class="fi-badge-label-ctn">
                <span class="fi-badge-label">
                    <?= e($label) ?>
                </span>
            </span>

            <?php if ($iconPosition === IconPosition::After) { ?>
                <?= $iconHtml ?>
                <?= $loadingIndicatorHtml ?>
            <?php } ?>
        </<?= ($tag === 'form') ? 'button' : $tag ?>>

        <?= ($tag === 'form') ? '</form>' : '' ?>

        <?php return ob_get_clean();
    }
}
