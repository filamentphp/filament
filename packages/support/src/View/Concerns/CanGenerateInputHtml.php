<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Components\ViewComponent;
use Filament\Support\View\ComponentAttributeBag;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanGenerateInputHtml
{
    use InteractsWithLivewireRendering;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    public function generateInputHtml(
        ?BaseComponentAttributeBag $attributes = null,
        bool $inlinePrefix = false,
        bool $inlineSuffix = false,
    ): string {
        $attributes ??= new ComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.input.index')) {
            return view('filament::components.input.index', [...get_defined_vars(), 'slot' => new ComponentSlot])->render();
        }

        return '<input ' . $attributes->class(['fi-input', 'fi-input-has-inline-prefix' => $inlinePrefix, 'fi-input-has-inline-suffix' => $inlineSuffix]) . ' />';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    public function generateInputSelectHtml(
        string | Htmlable $slot = '',
        ?BaseComponentAttributeBag $attributes = null,
        bool $inlinePrefix = false,
        bool $inlineSuffix = false,
    ): string {
        $attributes ??= new ComponentAttributeBag;
        $slot = $slot instanceof ComponentSlot ? $slot : new ComponentSlot(e($slot));

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.input.select')) {
            return view('filament::components.input.select', get_defined_vars())->render();
        }

        return '<select ' . $attributes->class(['fi-select-input', 'fi-select-input-has-inline-prefix' => $inlinePrefix]) . '>' . e($slot) . '</select>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<Htmlable>  $prefixActions  Already-visible affixes, retaining their objects for published views.
     * @param  string | array<int, string> | null  $prefixIconColor
     * @param  string | array<string> | null  $prefixIconAlias
     * @param  array<Htmlable>  $suffixActions  Already-visible affixes, retaining their objects for published views.
     * @param  string | array<int, string> | null  $suffixIconColor
     * @param  string | array<string> | null  $suffixIconAlias
     */
    public function generateInputWrapperHtml(
        string | Htmlable $slot = '',
        ?BaseComponentAttributeBag $attributes = null,
        ?string $alpineDisabled = null,
        ?string $alpineValid = null,
        bool $disabled = false,
        bool $inlinePrefix = false,
        bool $inlineSuffix = false,
        string | Htmlable | null $prefix = null,
        array $prefixActions = [],
        string | BackedEnum | Htmlable | null $prefixIcon = null,
        string | array | null $prefixIconColor = 'gray',
        string | array | null $prefixIconAlias = null,
        string | Htmlable | null $suffix = null,
        array $suffixActions = [],
        string | BackedEnum | Htmlable | null $suffixIcon = null,
        string | array | null $suffixIconColor = 'gray',
        string | array | null $suffixIconAlias = null,
        bool $valid = true,
    ): string {
        $attributes ??= new ComponentAttributeBag;
        $slot = $slot instanceof ComponentSlot ? $slot : new ComponentSlot(e($slot));

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.input.wrapper')) {
            return view('filament::components.input.wrapper', get_defined_vars())->render();
        }

        $hasAlpineDisabledClasses = filled($alpineDisabled);
        $hasAlpineValidClasses = filled($alpineValid);
        $wireTarget = $attributes->whereStartsWith(['wire:target'])->first();
        $hasLoadingIndicator = filled($wireTarget);
        $loadingIndicatorTarget = $hasLoadingIndicator ? html_entity_decode($wireTarget, ENT_QUOTES) : null;
        $loadingDelay = ($prefixIcon || $prefixIconAlias || $suffixIcon || $suffixIconAlias || $hasLoadingIndicator) ? config('filament.livewire_loading_delay', 'default') : null;
        $iconAttributes = (new ComponentAttributeBag)->merge([
            'wire:loading.remove.delay.' . $loadingDelay => $hasLoadingIndicator,
            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
        ], escape: false);
        $prefixIconHtml = ($prefixIcon || $prefixIconAlias) ? generate_icon_html($prefixIcon, $prefixIconAlias, $iconAttributes->color(IconComponent::class, $prefixIconColor)) : null;
        $suffixIconHtml = ($suffixIcon || $suffixIconAlias) ? generate_icon_html($suffixIcon, $suffixIconAlias, $iconAttributes->color(IconComponent::class, $suffixIconColor)) : null;
        $hasPrefix = count($prefixActions) || ($prefixIconHtml !== null) || filled($prefix);
        $hasSuffix = count($suffixActions) || ($suffixIconHtml !== null) || filled($suffix);

        ob_start(); ?>
        <div
            <?php if ($hasAlpineDisabledClasses || $hasAlpineValidClasses) { ?>
                x-bind:class="{ <?= e($hasAlpineDisabledClasses ? "'fi-disabled': {$alpineDisabled}," : '') ?> <?= e($hasAlpineValidClasses ? "'fi-invalid': ! ({$alpineValid})," : '') ?> }"
            <?php } ?>
            <?= $attributes->except(['wire:target', 'tabindex'])->class(['fi-input-wrp', 'fi-disabled' => (! $hasAlpineDisabledClasses) && $disabled, 'fi-invalid' => (! $hasAlpineValidClasses) && (! $valid)]) ?>
        >
            <?= $this->renderMorphMarkerHtml() ?>
            <?php if ($hasPrefix || $hasLoadingIndicator) { ?>
                <div
                    <?php if (! $hasPrefix) { ?> wire:loading.delay.<?= e($loadingDelay) ?>.flex wire:target="<?= e($loadingIndicatorTarget) ?>" wire:key="<?= e(Str::random()) ?>" <?php } ?>
                    <?php if (($prefixIconHtml !== null) || filled($prefix)) { ?> x-on:click="! $event.target.closest('.fi-input-wrp-actions') && $dispatch('focus-input')" <?php } ?>
                    <?= (new ComponentAttributeBag)->class(['fi-input-wrp-prefix', 'fi-input-wrp-prefix-has-content' => $hasPrefix, 'fi-inline' => $inlinePrefix, 'fi-input-wrp-prefix-has-label' => filled($prefix)]) ?>
                >
                    <?= $this->renderMorphMarkerHtml() ?>
                    <?php if (count($prefixActions)) { ?>
                        <div class="fi-input-wrp-actions"><?php foreach ($this->iterateRenderLoop($prefixActions) as $prefixAction) {
                            echo e($prefixAction);
                        } ?></div>
                    <?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <?= $this->renderMorphMarkerHtml() ?>
                    <?= e($prefixIconHtml) ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <?= $this->renderMorphMarkerHtml() ?>
                    <?php if ($hasLoadingIndicator) {
                        echo e(generate_loading_indicator_html((new ComponentAttributeBag(['wire:loading.delay.' . $loadingDelay => $hasPrefix, 'wire:target' => $hasPrefix ? $loadingIndicatorTarget : null]))->color(IconComponent::class, 'gray')));
                    } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <?php if (filled($prefix)) { ?><span class="fi-input-wrp-label"><?= e($prefix) ?></span><?php } ?>
                </div>
            <?php } ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
            <div
                <?php if ($hasLoadingIndicator && (! $hasPrefix)) { ?>
                    <?php if ($inlinePrefix) { ?> wire:loading.delay.<?= e($loadingDelay) ?>.class.remove="fi-input-wrp-content-ctn-ps" <?php } ?>
                    wire:target="<?= e($loadingIndicatorTarget) ?>"
                <?php } ?>
                <?= (new ComponentAttributeBag)->class(['fi-input-wrp-content-ctn', 'fi-input-wrp-content-ctn-ps' => $hasLoadingIndicator && (! $hasPrefix) && $inlinePrefix]) ?>
            ><?= e($slot) ?></div>
            <?php if ($hasSuffix) { ?>
                <div
                    <?php if (($suffixIconHtml !== null) || filled($suffix)) { ?> x-on:click="! $event.target.closest('.fi-input-wrp-actions') && $dispatch('focus-input')" <?php } ?>
                    <?= (new ComponentAttributeBag)->class(['fi-input-wrp-suffix', 'fi-inline' => $inlineSuffix, 'fi-input-wrp-suffix-has-label' => filled($suffix)]) ?>
                >
                    <?= $this->renderMorphMarkerHtml() ?>
                    <?php if (filled($suffix)) { ?><span class="fi-input-wrp-label"><?= e($suffix) ?></span><?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <?= $this->renderMorphMarkerHtml() ?>
                    <?= e($suffixIconHtml) ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <?php if (count($suffixActions)) { ?><div class="fi-input-wrp-actions"><?php foreach ($this->iterateRenderLoop($suffixActions) as $suffixAction) {
                        echo e($suffixAction);
                    } ?></div><?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }
}
