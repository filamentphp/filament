<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

trait HasJsRenderer
{
    use CanBeReadOnly;
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
        $statePath = $this->getStatePath();
        $stateBindingModifiers = $this->getStateBindingModifiers();
        $isLiveOnBlur = in_array('blur', $stateBindingModifiers);
        $debounceIndex = array_search('debounce', $stateBindingModifiers, strict: true);
        $debounce = ($debounceIndex !== false) ? ($stateBindingModifiers[$debounceIndex + 1] ?? 150) : null;

        if ($debounce !== null) {
            $debounce = (int) (((float) $debounce) * ((is_string($debounce) && str_ends_with($debounce, 's') && (! str_ends_with($debounce, 'ms'))) ? 1000 : 1));
        }

        $fieldConfiguration = [
            'id' => $this->getId(),
            'ariaDescribedBy' => implode(' ', $this->getDescriptionIds()),
            'isDisabled' => $this->isDisabled(),
            'isReadOnly' => $this->isReadOnly(),
            'isRequired' => $this->isRequired(),
            'isInvalid' => $this->hasValidationError(),
        ];

        $bindingConfiguration = [
            'isLive' => $isLiveOnBlur || in_array('live', $stateBindingModifiers),
            'isLiveOnBlur' => $isLiveOnBlur,
            'debounce' => $isLiveOnBlur ? null : $debounce,
        ];

        $key = $this->getLivewireKey() . '.' . md5(json_encode($bindingConfiguration) . $renderer);

        // Keep `x-data` stable when props change. Only the keyed props node is
        // replaced, so its `x-init` updates the renderer without remounting it.
        ob_start(); ?>

        <div
            wire:key="<?= e($key) ?>"
            x-bind:inert="isLoading"
            x-bind:aria-hidden="isLoading"
            inert
            aria-hidden="true"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('js-field', 'filament/forms')) ?>"
            x-data="jsFieldFormComponent({
                        state: $wire.$entangle(<?= e((string) Js::from($statePath)) ?>),
                        renderer: <?= e((string) Js::from($renderer)) ?>,
                        rendererConfiguration: JSON.parse($el.querySelector('[data-renderer-configuration]').dataset.rendererConfiguration),
                        fieldConfiguration: {
                            ...<?= Js::from($bindingConfiguration) ?>,
                            ...JSON.parse($el.querySelector('[data-renderer-configuration]').dataset.fieldConfiguration),
                        },
                    })"
            <?= $this->getExtraAttributeBag()->class(['fi-async-component-content'])->toHtml() ?>
        >
            <span
                hidden
                wire:key="<?= e($this->getLivewireKey() . '.renderer-configuration.' . md5(json_encode([$rendererConfiguration, $fieldConfiguration]))) ?>"
                data-renderer-configuration="<?= e(json_encode($rendererConfiguration)) ?>"
                data-field-configuration="<?= e(json_encode($fieldConfiguration)) ?>"
                x-init="updateRendererConfiguration(JSON.parse($el.dataset.rendererConfiguration), JSON.parse($el.dataset.fieldConfiguration))"
            ></span>
            <div wire:ignore x-ref="host"></div>
            <p x-cloak x-show="hasError" role="alert" class="fi-fo-field-wrp-error-message fi-async-component-error">
                <?= generate_icon_html(Heroicon::ExclamationCircle, size: IconSize::Small)->toHtml() ?>
                <span><?= e(__('filament::loading.error')) ?></span>
            </p>
        </div>

        <?php return $this->wrapEmbeddedHtml($this->wrapAsyncComponentHtml(ob_get_clean(), key: $key));
    }
}
