@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Icons\Heroicon;
    use Illuminate\Support\Js;

    $renderer = $this->getRenderer();
    $rendererConfiguration = $this->getRendererConfiguration();
@endphp

<x-filament-widgets::widget class="fi-wi-js">
    <x-filament::section>
        <div
            wire:key="{{ $this->getId() . '.renderer.' . md5((string) $renderer) }}.loading"
            x-data="{ isLoading: true }"
            x-on:async-component-ready.stop="isLoading = false"
            x-bind:data-loading="isLoading"
            data-loading="true"
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
                <span class="fi-sr-only">
                    {{ __('filament::loading.label') }}
                </span>

                <div aria-hidden="true" class="fi-loading-placeholder-content">
                    {{ $this->getLoadingPlaceholder() ?? view('filament::components.loading-placeholder') }}
                </div>
            </div>
            <div
                wire:key="{{ $this->getId() . '.renderer.' . md5((string) $renderer) }}"
                x-bind:inert="isLoading"
                x-bind:aria-hidden="isLoading"
                inert
                aria-hidden="true"
                class="fi-async-component-content"
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
                <p
                    x-cloak
                    x-show="hasError"
                    role="alert"
                    class="fi-async-component-error"
                >
                    {{ \Filament\Support\generate_icon_html(Heroicon::ExclamationCircle, size: IconSize::Small) }}
                    <span>
                        {{ __('filament::loading.error') }}
                    </span>
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
