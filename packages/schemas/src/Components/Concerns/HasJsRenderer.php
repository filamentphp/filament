<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Support\Concerns\HasLoadingPlaceholder;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

trait HasJsRenderer
{
    use HasLoadingPlaceholder;

    abstract public function getRenderer(): string | RawJs | null;

    /** @return array<string, mixed> */
    public function getRendererConfiguration(): array
    {
        return [];
    }

    public function toEmbeddedHtml(): string
    {
        $renderer = $this->getRenderer();
        $rendererConfiguration = $this->getRendererConfiguration();
        $componentConfiguration = ['id' => $this->getId()];
        $key = $this->getLivewireKey() . '.' . md5(json_encode($componentConfiguration) . $renderer);
        $loadingPlaceholder = $this->getLoadingPlaceholder() ?? view('filament::components.loading-placeholder');

        // Keep `x-data` stable when props change. Only the keyed props node is
        // replaced, so its `x-init` updates the renderer without remounting it.
        ob_start(); ?>

        <div
            x-data="{ isLoading: true }"
            x-on:async-component-ready.stop="isLoading = false"
            x-bind:data-loading="isLoading"
            data-loading="true"
            wire:key="<?= e($key) ?>.loading"
            class="fi-async-component"
        >
            <div
                x-show="isLoading"
                role="status"
                aria-live="polite"
                aria-busy="true"
                x-bind:aria-busy="isLoading ? 'true' : 'false'"
                class="fi-async-component-loading"
            >
                <span class="fi-sr-only"><?= e(__('filament::loading.label')) ?></span>

                <div aria-hidden="true" class="fi-loading-placeholder-content">
                    <?= ($loadingPlaceholder instanceof View) ? $loadingPlaceholder->render() : e($loadingPlaceholder) ?>
                </div>
            </div>
            <div
                wire:key="<?= e($key) ?>"
                x-bind:inert="isLoading"
                x-bind:aria-hidden="isLoading"
                inert
                aria-hidden="true"
                x-load
                x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('js-component', 'filament/schemas')) ?>"
                x-data="jsSchemaComponent({
                    renderer: <?= e((string) Js::from($renderer)) ?>,
                    rendererConfiguration: JSON.parse($el.querySelector('[data-renderer-configuration]').dataset.rendererConfiguration),
                    componentConfiguration: <?= Js::from($componentConfiguration) ?>,
                })"
                <?= $this->getExtraAttributeBag()->class(['fi-async-component-content'])->toHtml() ?>
            >
                <span
                    hidden
                    wire:key="<?= e($this->getLivewireKey() . '.renderer-configuration.' . md5(json_encode($rendererConfiguration))) ?>"
                    data-renderer-configuration="<?= e(json_encode($rendererConfiguration)) ?>"
                    x-init="updateRendererConfiguration(JSON.parse($el.dataset.rendererConfiguration))"
                ></span>
                <div wire:ignore x-ref="host"></div>
                <p x-cloak x-show="hasError" role="alert" class="fi-async-component-error">
                    <?= generate_icon_html(Heroicon::ExclamationCircle, size: IconSize::Small)->toHtml() ?>
                    <span><?= e(__('filament::loading.error')) ?></span>
                </p>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
