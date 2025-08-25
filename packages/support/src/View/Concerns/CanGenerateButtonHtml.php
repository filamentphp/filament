<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\ButtonComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanGenerateButtonHtml
{
    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     *
     * @param  string | array<string> | null  $badgeColor,
     * @param  string | array<string> | null  $color,
     * @param  array<string>  $keyBindings
     */
    public function generateButtonHtml(
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
        bool $isOutlined = false,
        ?array $keyBindings = null,
        string | Htmlable | null $label = null,
        ?string $labeledFromBreakpoint = null,
        Size | string | null $size = null,
        string $tag = 'button',
        ?string $target = null,
        ?string $tooltip = null,
        ?string $type = 'button',
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

        $wireTarget = $hasLoadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

        $hasFormProcessingLoadingIndicator = $type === 'submit' && filled($form);
        $hasLoadingIndicator = filled($wireTarget) || $hasFormProcessingLoadingIndicator;

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
                'aria-label' => $isLabelSrOnly ? trim(strip_tags(e($label))) : null,
                'disabled' => $isDisabled && blank($tooltip),
                'form' => $formId,
                'type' => match ($tag) {
                    'button' => $type,
                    'form' => 'submit',
                    default => null,
                },
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                'x-bind:disabled' => $hasFormProcessingLoadingIndicator ? 'isProcessing' : null,
                'x-bind:aria-label' => ($isLabelSrOnly && $hasFormProcessingLoadingIndicator) ? ('isProcessing ? processingMessage : ' . Js::from(trim(strip_tags(e($label))))) : null,
            ], escape: false)
            ->when(
                $isDisabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            );

        $buttonAttributes = $attributes
            ->class([
                'fi-btn',
                'fi-disabled' => $isDisabled,
                'fi-outlined' => $isOutlined,
                ($size instanceof Size) ? "fi-size-{$size->value}" : $size,
                is_string($labeledFromBreakpoint) ? "fi-labeled-from-{$labeledFromBreakpoint}" : null,
            ])
            ->color(app(ButtonComponent::class, ['isOutlined' => $isOutlined]), $color);

        $iconHtml = $icon ? generate_icon_html($icon, $iconAlias, (new ComponentAttributeBag([
            'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
        ])), size: $iconSize)->toHtml() : '';

        $loadingIndicatorHtml = $hasLoadingIndicator ? generate_loading_indicator_html((new ComponentAttributeBag([
            'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
            'wire:target' => $loadingIndicatorTarget,
        ])), size: $iconSize)->toHtml() : '';

        $formProcessingLoadingIndicatorHtml = $hasFormProcessingLoadingIndicator ? generate_loading_indicator_html((new ComponentAttributeBag([
            'x-cloak' => 'x-cloak',
            'x-show' => 'isProcessing',
        ])), size: $iconSize)->toHtml() : '';

        ob_start(); ?>

        <?php if ($labeledFromBreakpoint) { ?>
            <?= $this->generateIconButtonHtml(
                attributes: $attributes,
                badge: $badge,
                badgeColor: $badgeColor,
                badgeSize: $badgeSize,
                color: $color,
                form: $form,
                formId: $formId,
                hasLoadingIndicator: $hasLoadingIndicator,
                hasSpaMode: $hasSpaMode,
                href: $href,
                icon: $icon,
                iconAlias: $iconAlias,
                iconSize: $iconSize,
                isDisabled: $isDisabled,
                keyBindings: $keyBindings,
                label: $label,
                size: $size,
                tag: $tag,
                target: $target,
                tooltip: $tooltip,
                type: $type,
            ) ?>
        <?php } ?>

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
            <?php if ($hasFormProcessingLoadingIndicator) { ?>
                x-data="filamentFormButton"
                x-bind:class="{ 'fi-processing': isProcessing }"
            <?php } ?>
            <?= $buttonAttributes->toHtml() ?>
        >
            <?php if ($iconPosition === IconPosition::Before) { ?>
                <?= $iconHtml ?>
                <?= $loadingIndicatorHtml ?>
                <?= $formProcessingLoadingIndicatorHtml ?>
            <?php } ?>

            <?php if (! $isLabelSrOnly) { ?>
                <?php if ($hasFormProcessingLoadingIndicator) { ?>
                    <span x-show="! isProcessing">
                        <?= e($label) ?>
                    </span>
                <?php } else { ?>
                    <?= e($label) ?>
                <?php } ?>
            <?php } ?>

            <?php if ($hasFormProcessingLoadingIndicator && (! $isLabelSrOnly)) { ?>
                <span
                    x-cloak
                    x-show="isProcessing"
                    x-text="processingMessage"
                ></span>
            <?php } ?>

            <?php if ($iconPosition === IconPosition::After) { ?>
                <?= $iconHtml ?>
                <?= $loadingIndicatorHtml ?>
                <?= $formProcessingLoadingIndicatorHtml ?>
            <?php } ?>

            <?php if (filled($badge)) { ?>
                <div class="fi-btn-badge-ctn">
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
