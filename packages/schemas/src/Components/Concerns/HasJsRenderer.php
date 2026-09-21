<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Illuminate\Support\Js;

trait HasJsRenderer
{
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

        // Keep `x-data` stable when props change. Only the keyed props node is
        // replaced, so its `x-init` updates the renderer without remounting it.
        ob_start(); ?>

        <div
            wire:key="<?= e($this->getLivewireKey() . '.' . md5(json_encode($componentConfiguration) . $renderer)) ?>"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('js-component', 'filament/schemas')) ?>"
            x-data="jsSchemaComponent({
                renderer: <?= e((string) Js::from($renderer)) ?>,
                rendererConfiguration: JSON.parse($el.querySelector('[data-renderer-configuration]').dataset.rendererConfiguration),
                componentConfiguration: <?= Js::from($componentConfiguration) ?>,
            })"
            <?= $this->getExtraAttributeBag()->toHtml() ?>
        >
            <span
                hidden
                wire:key="<?= e($this->getLivewireKey() . '.renderer-configuration.' . md5(json_encode($rendererConfiguration))) ?>"
                data-renderer-configuration="<?= e(json_encode($rendererConfiguration)) ?>"
                x-init="updateRendererConfiguration(JSON.parse($el.dataset.rendererConfiguration))"
            ></span>
            <div wire:ignore x-ref="host"></div>
            <p x-cloak x-show="hasError" role="alert">
                <?= e(__('filament-schemas::components.js_component.failed_to_load')) ?>
            </p>
        </div>

        <?php return ob_get_clean();
    }
}
