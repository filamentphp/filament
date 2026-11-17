<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanGenerateBadgeHtml
{
    use InteractsWithLivewireRendering;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  string | array<string> | null  $color
     * @param  string | array<string> | null  $iconAlias
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
        string | array | null $iconAlias = null,
        ?IconPosition $iconPosition = IconPosition::Before,
        IconSize | string | null $iconSize = null,
        bool $isDisabled = false,
        bool $isDisabledDuringLoading = true,
        ?array $keyBindings = null,
        string | Htmlable | null $label = null,
        Size | string | null $size = null,
        string $tag = 'span',
        ?string $target = null,
        string | Htmlable | null $tooltip = null,
        ?string $type = 'button',
        ?ComponentSlot $deleteButton = null,
        bool $shouldCheckForPublishedView = false,
    ): string {
        if ($shouldCheckForPublishedView && ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.badge')) {
            if ($attributes->has('wire:key')) {
                $this->setRenderKey($attributes->get('wire:key'));
            }

            return view('filament::components.badge', [
                'attributes' => $attributes,
                'color' => $color,
                'deleteButton' => $deleteButton,
                'disabled' => $isDisabled,
                'form' => $form,
                'formId' => $formId,
                'href' => $href,
                'icon' => $icon,
                'iconAlias' => $iconAlias,
                'iconPosition' => $iconPosition,
                'iconSize' => $iconSize,
                'keyBindings' => $keyBindings,
                'loadingIndicator' => $hasLoadingIndicator,
                'size' => $size,
                'slot' => $label instanceof ComponentSlot ? $label : new ComponentSlot(e($label)),
                'spaMode' => $hasSpaMode,
                'tag' => $tag,
                'target' => $target,
                'tooltip' => $tooltip,
                'type' => $type,
            ])->render();
        }

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
                'disabled' => $isDisabled && blank($tooltip),
                'form' => $tag === 'button' ? $formId : null,
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

        $isDeletable = count($deleteButton?->attributes->getAttributes() ?? []) > 0;

        $iconHtml = ($icon || $iconAlias) ? generate_icon_html($icon, $iconAlias, size: $iconSize ?? IconSize::Small)?->toHtml() ?? '' : '';

        $loadingIndicatorHtml = $hasLoadingIndicator ? generate_loading_indicator_html(new FilamentComponentAttributeBag([
            'wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') => $hasSeparateLoadingIndicatorTarget ? 'fi-loading' : null,
            'wire:target' => $hasSeparateLoadingIndicatorTarget ? $loadingIndicatorTarget : null,
        ]), size: $iconSize ?? IconSize::Small)->toHtml() : '';

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
            <?= $this->generateMorphMarkerHtml() ?>
            <?php if ($iconPosition === IconPosition::Before) { ?>
                <?= $this->generateMorphMarkerHtml() ?>
                <?= $loadingIndicatorHtml ?>
                <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
                <?= $iconHtml ?>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true) ?>

            <span class="fi-badge-label-ctn">
                <span class="fi-badge-label">
                    <?= e($label) ?>
                </span>
            </span>

            <?php if ($isDeletable) {
                $deleteButtonWireTarget = $deleteButton->attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(static fn ($value): bool => filled($value))->first();
                $deleteButtonHasLoadingIndicator = filled($deleteButtonWireTarget);
                $deleteButtonLoadingIndicatorTarget = $deleteButtonHasLoadingIndicator ? html_entity_decode($deleteButtonWireTarget, ENT_QUOTES) : null;
                $deleteButtonHasSeparateLoadingIndicatorTarget = $deleteButtonHasLoadingIndicator && $deleteButton->attributes->has('wire:target') && blank($deleteButton->attributes->get('wire:target'));
                ?>
                <button data-prevent-disabled-clicks type="button" <?= $deleteButton->attributes->except(['label'])->merge([
                    'wire:loading.attr' => $deleteButtonHasLoadingIndicator ? 'aria-disabled' : null,
                    'wire:loading.class' => $deleteButtonHasLoadingIndicator ? $deleteButton->attributes->prepends('fi-disabled') : null,
                    'wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') => ($deleteButtonHasLoadingIndicator && (! $deleteButtonHasSeparateLoadingIndicatorTarget)) ? $deleteButton->attributes->prepends('fi-loading') : null,
                    'wire:target' => $deleteButtonLoadingIndicatorTarget,
                ], escape: false)->class(['fi-badge-delete-btn']) ?>>
                    <?= $this->generateMorphMarkerHtml() ?>
                    <?php if ($deleteButtonHasLoadingIndicator) {
                        echo e(generate_loading_indicator_html(new FilamentComponentAttributeBag([
                            'wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') => $deleteButtonHasSeparateLoadingIndicatorTarget ? 'fi-loading' : null,
                            'wire:target' => $deleteButtonHasSeparateLoadingIndicatorTarget ? $deleteButtonLoadingIndicatorTarget : null,
                        ]), size: IconSize::ExtraSmall));
                    } ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
                    <span aria-hidden="true" class="fi-badge-delete-btn-icon"></span>
                    <span class="fi-sr-only"><?= e(filled($deleteLabel = $deleteButton->attributes->get('label')) ? $deleteLabel : __('filament::components/badge.actions.delete.label')) ?></span>
                </button>
            <?php } elseif ($iconPosition === IconPosition::After) { ?>
                <?= $this->generateMorphMarkerHtml() ?>
                <?= $loadingIndicatorHtml ?>
                <?= $this->generateMorphMarkerHtml(isEnd: true) ?>
                <?= $iconHtml ?>
            <?php } ?>
        </<?= ($tag === 'form') ? 'button' : $tag ?>>

        <?= ($tag === 'form') ? '</form>' : '' ?>

        <?php return ob_get_clean();
    }
}
