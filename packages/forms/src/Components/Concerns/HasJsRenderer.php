<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Illuminate\Support\Js;

trait HasJsRenderer
{
    use CanBeReadOnly;

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
        $statePath = $this->getStatePath();
        $stateBindingModifiers = $this->getStateBindingModifiers();
        $isLiveOnBlur = in_array('blur', $stateBindingModifiers);
        $debounceIndex = array_search('debounce', $stateBindingModifiers, strict: true);
        $debounce = ($debounceIndex !== false) ? ($stateBindingModifiers[$debounceIndex + 1] ?? 150) : null;

        if ($debounce !== null) {
            $debounce = (int) (((float) $debounce) * ((is_string($debounce) && str_ends_with($debounce, 's') && (! str_ends_with($debounce, 'ms'))) ? 1000 : 1));
        }

        $configuration = [
            'id' => $this->getId(),
            'ariaDescribedBy' => $this->getId() . '-description',
            'disabled' => $this->isDisabled(),
            'readOnly' => $this->isReadOnly(),
            'required' => $this->isRequired(),
            'invalid' => $this->hasErrorForPath($statePath),
            'isLive' => $isLiveOnBlur || in_array('live', $stateBindingModifiers),
            'isLiveOnBlur' => $isLiveOnBlur,
            'debounce' => $isLiveOnBlur ? null : $debounce,
        ];

        // Keep `x-data` stable when props change. Only the keyed props node is
        // replaced, so its `x-init` updates the renderer without remounting it.
        ob_start(); ?>

        <div
            wire:key="<?= e($this->getLivewireKey() . '.' . md5(json_encode($configuration) . $renderer)) ?>"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('js-field', 'filament/forms')) ?>"
            x-data="jsFieldFormComponent({
                        state: $wire.$entangle(<?= e((string) Js::from($statePath)) ?>),
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
            <p x-cloak x-show="hasError" role="alert" class="fi-fo-field-wrp-error-message">
                <?= e(__('filament-forms::components.js_field.failed_to_load')) ?>
            </p>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), descriptionId: $configuration['ariaDescribedBy']);
    }
}
