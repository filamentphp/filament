<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\IconButtonComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanGenerateIconButtonHtml
{
    use InteractsWithLivewireRendering;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  string | array<string> | null  $badgeColor,
     * @param  string | array<string> | null  $color,
     * @param  string | array<string> | null  $iconAlias
     * @param  array<string>  $keyBindings
     */
    public function generateIconButtonHtml(
        ComponentAttributeBag $attributes,
        string | Htmlable | null $badge = null,
        string | array | null $badgeColor = null,
        Size | string | null $badgeSize = null,
        string | array | null $color = 'primary',
        ?string $form = null,
        ?string $formId = null,
        bool $hasLoadingIndicator = true,
        ?bool $hasSpaMode = null,
        ?string $href = null,
        string | BackedEnum | Htmlable | null $icon = null,
        string | array | null $iconAlias = null,
        IconSize | string | null $iconSize = null,
        bool $isDisabled = false,
        bool $isDisabledDuringLoading = true,
        ?array $keyBindings = null,
        string | Htmlable | null $label = null,
        Size | string | null $size = null,
        string $tag = 'button',
        ?string $target = null,
        string | Htmlable | null $tooltip = null,
        ?string $type = 'button',
        bool $shouldCheckForPublishedView = false,
    ): string {
        if ($shouldCheckForPublishedView && ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.icon-button')) {
            return view('filament::components.icon-button', [
                'attributes' => $attributes,
                'badge' => $badge,
                'badgeColor' => $badgeColor,
                'badgeSize' => $badgeSize,
                'color' => $color,
                'disabled' => $isDisabled,
                'form' => $form,
                'formId' => $formId,
                'href' => $href,
                'icon' => $icon,
                'iconAlias' => $iconAlias,
                'iconSize' => $iconSize,
                'keyBindings' => $keyBindings,
                'label' => $label,
                'loadingIndicator' => $hasLoadingIndicator,
                'size' => $size,
                'slot' => new ComponentSlot,
                'spaMode' => $hasSpaMode,
                'tag' => $tag,
                'target' => $target,
                'tooltip' => $tooltip,
                'type' => $type,
            ])->render();
        }

        $badgeColor ??= 'primary';
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

        $hasSeparateLoadingIndicatorTarget = $hasLoadingIndicator && $attributes->has('wire:target') && blank($attributes->get('wire:target'));

        $hasTooltip = filled($tooltip);

        $formAttributes = $attributes->only(['action', 'method', 'wire:submit']);

        $attributes = $attributes
            ->when(
                $tag === 'form',
                fn (ComponentAttributeBag $attributes) => $attributes->except(['action', 'class', 'method', 'wire:submit']),
            )
            ->merge([
                'aria-disabled' => $isDisabled ? 'true' : null,
                // Security: These attributes are rendered without escaping, so the `aria-label` must be escaped here, otherwise an `Htmlable` label could break out of the attribute. `doubleEncode: false` preserves entities that Blade has already escaped in a string label.
                'aria-label' => filled($label) ? e(trim(strip_tags($label instanceof Htmlable ? $label->toHtml() : $label)), doubleEncode: false) : null,
                'disabled' => $isDisabled && blank($tooltip),
                'form' => $formId,
                'tabindex' => (($tag === 'a') && $isDisabled && $hasTooltip) ? '0' : null,
                'type' => match ($tag) {
                    'button' => $type,
                    'form' => 'submit',
                    default => null,
                },
                'wire:loading.attr' => ($tag === 'button' && $isDisabledDuringLoading) ? 'aria-disabled' : null,
                'wire:loading.class' => ($tag === 'button' && $isDisabledDuringLoading) ? $attributes->prepends('fi-disabled') : null,
                'wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') => ($hasLoadingIndicator && (! $hasSeparateLoadingIndicatorTarget)) ? $attributes->prepends('fi-loading') : null,
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
            <?= $this->renderMorphMarkerHtml() ?>
            <?= $hasLoadingIndicator ? generate_loading_indicator_html(new FilamentComponentAttributeBag([
                'wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') => $hasSeparateLoadingIndicatorTarget ? 'fi-loading' : null,
                'wire:target' => $hasSeparateLoadingIndicatorTarget ? $loadingIndicatorTarget : null,
            ]), size: $iconSize)->toHtml() : '' ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
            <?= $this->renderMorphMarkerHtml() ?>
            <?= ($icon || $iconAlias) ? generate_icon_html($icon, $iconAlias, size: $iconSize)?->toHtml() ?? '' : '' ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true) ?>

            <?php if (filled($badge)) { ?>
                <div class="fi-icon-btn-badge-ctn">
                    <?php if ($badge instanceof ComponentSlot) { ?>
                        <?= e($badge) ?>
                    <?php } else { ?>
                    <span <?= (new FilamentComponentAttributeBag)->color(BadgeComponent::class, $badgeColor)->class([
                        'fi-badge',
                        ($badgeSize instanceof Size) ? "fi-size-{$badgeSize->value}" : $badgeSize,
                    ])->toHtml() ?>>
                        <?= e($badge) ?>
                    </span>
                    <?php } ?>
                </div>
            <?php } ?>
        </<?= ($tag === 'form') ? 'button' : $tag ?>>

        <?= ($tag === 'form') ? '</form>' : '' ?>

        <?php return ob_get_clean();
    }
}
