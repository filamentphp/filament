<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\IconButtonComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;
use function Filament\Support\get_component_color_classes;

trait CanGenerateIconButtonHtml
{
    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     *
     * @param  array<string>  $keyBindings
     */
    public function generateIconButtonHtml(
        ComponentAttributeBag $attributes,
        string | Htmlable | null $badge = null,
        ?string $badgeColor = null,
        Size | string | null $badgeSize = null,
        ?string $color = 'primary',
        ?string $form = null,
        ?string $formId = null,
        bool $hasLoadingIndicator = true,
        ?bool $hasSpaMode = null,
        ?string $href = null,
        string | BackedEnum | Htmlable | null $icon = null,
        ?string $iconAlias = null,
        IconSize | string | null $iconSize = null,
        bool $isDisabled = false,
        ?array $keyBindings = null,
        string | Htmlable | null $label = null,
        Size | string | null $size = null,
        string $tag = 'button',
        ?string $target = null,
        ?string $tooltip = null,
        ?string $type = 'button',
    ): string {
        $color ??= 'primary';

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
            Size::ExtraSmall => IconSize::Small,
            Size::Large, Size::ExtraLarge => IconSize::Large,
            default => null,
        };

        $wireTarget = $hasLoadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

        $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

        if ($hasLoadingIndicator) {
            $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
        }

        $hasTooltip = filled($tooltip);

        $attributes = $attributes
            ->merge([
                'aria-disabled' => $isDisabled ? 'true' : null,
                'aria-label' => $label,
                'disabled' => $isDisabled && blank($tooltip),
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->merge([
                'title' => $hasTooltip ? null : $label,
            ], escape: true)
            ->when(
                $isDisabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'fi-icon-btn',
                'fi-disabled' => $isDisabled,
                ($size instanceof Size) ? "fi-size-{$size->value}" : $size,
            ])
            ->color(IconButtonComponent::class, $color);

        ob_start(); ?>

        <<?= $tag ?>
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
            ])), size: $iconSize)->toHtml() : '' ?>
            <?= $hasLoadingIndicator ? generate_loading_indicator_html((new ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ])), size: $iconSize)->toHtml() : '' ?>

            <?php if (filled($badge)) { ?>
                <div class="fi-icon-btn-badge-ctn">
                    <span class="<?= Arr::toCssClasses([
                        'fi-badge',
                        ...get_component_color_classes(BadgeComponent::class, $badgeColor),
                        ($badgeSize instanceof Size) ? "fi-size-{$badgeSize->value}" : $badgeSize,
                    ]) ?>">
                        <?= e($badge) ?>
                    </span>
                </div>
            <?php } ?>
        </<?= $tag ?>>

        <?php return ob_get_clean();
    }
}
