@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\Support\Js;

    $renderer = $this->getRenderer();
    $rendererProps = $this->getRendererProps();
@endphp

<x-filament-widgets::widget class="fi-wi-js">
    <x-filament::section>
        <div
            wire:key="{{ $this->getId() . '.renderer.' . md5((string) $renderer) }}"
            x-load
            x-load-src="{{ FilamentAsset::getAlpineComponentSrc('js-widget', 'filament/widgets') }}"
            x-data="jsWidgetComponent({
                        renderer: {{ (string) Js::from($renderer) }},
                        rendererProps: JSON.parse(
                            $el.querySelector('[data-renderer-props]').dataset.rendererProps,
                        ),
                    })"
        >
            {{-- Keep `x-data` stable so PHP prop updates do not remount the renderer. --}}
            <span
                hidden
                wire:key="{{ $this->getId() . '.renderer-props.' . md5(json_encode($rendererProps)) }}"
                data-renderer-props="{{ json_encode($rendererProps) }}"
                x-init="updateRendererProps(JSON.parse($el.dataset.rendererProps))"
            ></span>
            <div wire:ignore x-ref="host"></div>
            <p x-cloak x-show="hasError" role="alert">
                {{ __('filament-widgets::js-widget.failed_to_load') }}
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
