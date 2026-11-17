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
                'wire:loading.attr' => $tag === 'button' ? 'aria-disabled' : null,
                'wire:loading.class' => $tag === 'button' ? $attributes->prepends('fi-disabled') : null,
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

        $loadingDelay = ($icon || $iconAlias || $hasLoadingIndicator || $isDeletable)
            ? config('filament.livewire_loading_delay', 'default')
            : null;

        $iconHtml = ($icon || $iconAlias) ? generate_icon_html($icon, $iconAlias, (new FilamentComponentAttributeBag([
            'wire:loading.remove.delay.' . $loadingDelay => $hasLoadingIndicator,
            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
        ])), size: $iconSize ?? IconSize::Small)?->toHtml() ?? '' : '';

        $loadingIndicatorHtml = $hasLoadingIndicator ? generate_loading_indicator_html((new FilamentComponentAttributeBag([
            'wire:loading.delay.' . $loadingDelay => '',
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
            <?php if ($iconPosition === IconPosition::Before) { ?>
                <?= $this->renderMorphMarkerHtml() ?>
                <?= $iconHtml ?>
                <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                <?= $loadingIndicatorHtml ?>
            <?php } ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true) ?>

            <span class="fi-badge-label-ctn">
                <span class="fi-badge-label">
                    <?= e($label) ?>
                </span>
            </span>

            <?php if ($isDeletable) {
                $deleteButtonWireTarget = $deleteButton->attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(static fn ($value): bool => filled($value))->first();
                $deleteButtonHasLoadingIndicator = filled($deleteButtonWireTarget);
                $deleteButtonLoadingIndicatorTarget = $deleteButtonHasLoadingIndicator ? html_entity_decode($deleteButtonWireTarget, ENT_QUOTES) : null;
                ?>
                <button data-prevent-disabled-clicks type="button" <?= $deleteButton->attributes->except(['label'])->merge([
                    'wire:loading.attr' => $deleteButtonHasLoadingIndicator ? 'aria-disabled' : null,
                    'wire:loading.class' => $deleteButtonHasLoadingIndicator ? $deleteButton->attributes->prepends('fi-disabled') : null,
                    'wire:target' => $deleteButtonLoadingIndicatorTarget,
                ], escape: false)->class(['fi-badge-delete-btn']) ?>>
                    <span <?= (new FilamentComponentAttributeBag([
                        'aria-hidden' => 'true',
                        'wire:loading.remove.delay.' . $loadingDelay => $deleteButtonHasLoadingIndicator,
                        'wire:target' => $deleteButtonHasLoadingIndicator ? $deleteButtonLoadingIndicatorTarget : false,
                    ]))->class(['fi-badge-delete-btn-icon']) ?>></span>
                    <?= $this->renderMorphMarkerHtml() ?>
                    <?php if ($deleteButtonHasLoadingIndicator) {
                        echo e(generate_loading_indicator_html(new FilamentComponentAttributeBag([
                            'wire:loading.delay.' . $loadingDelay => '',
                            'wire:target' => $deleteButtonLoadingIndicatorTarget,
                        ]), size: IconSize::ExtraSmall));
                    } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <span class="fi-sr-only"><?= e(filled($deleteLabel = $deleteButton->attributes->get('label')) ? $deleteLabel : __('filament::components/badge.actions.delete.label')) ?></span>
                </button>
            <?php } elseif ($iconPosition === IconPosition::After) { ?>
                <?= $this->renderMorphMarkerHtml() ?>
                <?= $iconHtml ?>
                <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                <?= $loadingIndicatorHtml ?>
            <?php } ?>
        </<?= ($tag === 'form') ? 'button' : $tag ?>>

        <?= ($tag === 'form') ? '</form>' : '' ?>

        <?php return ob_get_clean();
    }
}
