<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\LinkComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanGenerateLinkHtml
{
    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     *
     * @param  string | array<string> | null  $badgeColor,
     * @param  string | array<string> | null  $color,
     * @param  array<string>  $keyBindings
     */
    public function generateLinkHtml(
        ComponentAttributeBag $attributes,
        string | Htmlable | null $badge = null,
        string | array | null $badgeColor = 'primary',
        Size | string | null $badgeSize = null,
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
        bool $isLabelSrOnly = false,
        ?array $keyBindings = null,
        string | Htmlable | null $label = null,
        Size | string | null $size = null,
        string $tag = 'button',
        ?string $target = null,
        string | Htmlable | null $tooltip = null,
        ?string $type = 'button',
        string | FontWeight | null $weight = null,
    ): string {
        $color ??= 'primary';

        if (! $iconPosition instanceof IconPosition) {
            $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
        }

        if (! $size instanceof Size) {
            $size = filled($size) ? (Size::tryFrom($size) ?? $size) : Size::Medium;
        }

        if (! $badgeSize instanceof Size) {
            $badgeSize = filled($badgeSize) ? (Size::tryFrom($badgeSize) ?? $badgeSize) : Size::ExtraSmall;
        }

        if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
            $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
        }

        $iconSize ??= match ($size) {
            Size::ExtraSmall, Size::Small => IconSize::Small,
            default => null,
        };

        if (! $weight instanceof FontWeight) {
            $weight = filled($weight) ? (FontWeight::tryFrom($weight) ?? $weight) : null;
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
                'form' => $formId,
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
                'fi-link',
                'fi-disabled' => $isDisabled,
                ($size instanceof Size) ? "fi-size-{$size->value}" : $size,
                ($weight instanceof FontWeight) ? "fi-font-{$weight->value}" : $weight,
            ])
            ->color(LinkComponent::class, $color);

        $iconHtml = $icon ? generate_icon_html($icon, $iconAlias, (new ComponentAttributeBag([
            'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
        ])), size: $iconSize)->toHtml() : '';

        $loadingIndicatorHtml = $hasLoadingIndicator ? generate_loading_indicator_html((new ComponentAttributeBag([
            'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
            'wire:target' => $loadingIndicatorTarget,
        ])), size: $iconSize)->toHtml() : '';

        ob_start(); ?>

        <?= ($tag === 'form') ? ('<form ' . $formAttributes->toHtml() . '>' . csrf_field()) : '' ?>

        <<?= ($tag === 'form') ? 'button' : $tag ?>
            <?php if (($tag === 'a') && (! ($isDisabled && $hasTooltip))) { ?>
                <?= generate_href_html($href, $target === '_blank', $hasSpaMode)->toHtml() ?>
            <?php } ?>
            <?php if ($keyBindings) { ?>
                x-bind:id="$id('key-bindings')"
                x-mousetrap.global.<?= collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') ?>="document.getElementById($el.id)?.click()"
            <?php } ?>
            <?php if ($hasTooltip) { ?>
                x-tooltip="{
                    content: <?= Js::from($tooltip) ?>,
                    theme: $store.theme,
                    allowHTML: <?= Js::from($tooltip instanceof Htmlable) ?>,
                }"
            <?php } ?>
            <?= $attributes->toHtml() ?>
        >
            <?php if ($iconPosition === IconPosition::Before) { ?>
                <?= $iconHtml ?>
                <?= $loadingIndicatorHtml ?>
            <?php } ?>

            <?php if (! $isLabelSrOnly) { ?>
                <?= e($label) ?>
            <?php } ?>

            <?php if ($iconPosition === IconPosition::After) { ?>
                <?= $iconHtml ?>
                <?= $loadingIndicatorHtml ?>
            <?php } ?>

            <?php if (filled($badge)) { ?>
                <div class="fi-link-badge-ctn">
                    <span <?= (new ComponentAttributeBag)->color(BadgeComponent::class, $badgeColor)->class([
                        'fi-badge',
                        ($badgeSize instanceof Size) ? "fi-size-{$badgeSize->value}" : $badgeSize,
                    ])->toHtml() ?>>
                        <?= e($badge) ?>
                    </span>
                </div>
            <?php } ?>
        </<?= ($tag === 'form') ? 'button' : $tag ?>>

        <?= ($tag === 'form') ? '</form>' : '' ?>

        <?php return ob_get_clean();
    }
}
