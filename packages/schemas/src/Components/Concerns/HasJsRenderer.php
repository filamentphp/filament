<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Illuminate\Support\Js;

trait HasJsRenderer
{
    abstract public function getRenderer(): string | RawJs | null;

    /** @return array<string, mixed> */
    public function getRendererProps(): array
    {
        return [];
    }

    public function toEmbeddedHtml(): string
    {
        $renderer = $this->getRenderer();
        $rendererProps = $this->getRendererProps();
        $configuration = ['id' => $this->getId()];

        // Keep `x-data` stable when props change. Only the keyed props node is
        // replaced, so its `x-init` updates the renderer without remounting it.
        ob_start(); ?>

        <div
            wire:key="<?= e($this->getLivewireKey() . '.' . md5(json_encode($configuration) . $renderer)) ?>"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('js-component', 'filament/schemas')) ?>"
            x-data="jsSchemaComponent({
                renderer: <?= e((string) Js::from($renderer)) ?>,
                rendererProps: JSON.parse($el.querySelector('[data-renderer-props]').dataset.rendererProps),
                configuration: <?= Js::from($configuration) ?>,
            })"
            <?= $this->getExtraAttributeBag()->toHtml() ?>
        >
            <span
                hidden
                wire:key="<?= e($this->getLivewireKey() . '.renderer-props.' . md5(json_encode($rendererProps))) ?>"
                data-renderer-props="<?= e(json_encode($rendererProps)) ?>"
                x-init="updateRendererProps(JSON.parse($el.dataset.rendererProps))"
            ></span>
            <div wire:ignore x-ref="host"></div>
            <p x-cloak x-show="hasError" role="alert">
                <?= e(__('filament-schemas::components.js_component.failed_to_load')) ?>
            </p>
        </div>

        <?php return ob_get_clean();
    }
}
