@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\Support\Js;

    $renderer = $this->getRenderer();
    $rendererConfiguration = $this->getRendererConfiguration();
@endphp

<x-filament-widgets::widget class="fi-wi-js">
    <x-filament::section>
        <div
            wire:key="{{ $this->getId() . '.renderer.' . md5((string) $renderer) }}"
            x-load
            x-load-src="{{ FilamentAsset::getAlpineComponentSrc('js-widget', 'filament/widgets') }}"
            x-data="jsWidgetComponent({
                        renderer: {{ (string) Js::from($renderer) }},
                        rendererConfiguration: JSON.parse(
                            $el.querySelector('[data-renderer-configuration]').dataset
                                .rendererConfiguration,
                        ),
                    })"
        >
            {{-- Keep `x-data` stable so PHP prop updates do not remount the renderer. --}}
            <span
                hidden
                wire:key="{{ $this->getId() . '.renderer-configuration.' . md5(json_encode($rendererConfiguration)) }}"
                data-renderer-configuration="{{ json_encode($rendererConfiguration) }}"
                x-init="updateRendererConfiguration(JSON.parse($el.dataset.rendererConfiguration))"
            ></span>
            <div wire:ignore x-ref="host"></div>
            <p x-cloak x-show="hasError" role="alert">
                {{ __('filament-widgets::js-widget.failed_to_load') }}
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
