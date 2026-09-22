@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <div data-indicator-comparison>
        <section>
            <h2>Blade</h2>
            <div
                data-indicator-row="blade"
                role="status"
                aria-label="Loading content"
            >
                @foreach ($this->getIndicatorCases() as $attributes)
                    {{ \Filament\Support\generate_loading_indicator_html(new ComponentAttributeBag(array_diff_key($attributes, ['size' => true])), isset($attributes['size']) ? IconSize::from($attributes['size']) : null) }}
                @endforeach
            </div>
        </section>
        @foreach (['react', 'vue', 'svelte'] as $framework)
            <section
                x-data="{
                    renderer: null,
                    isDestroyed: false,
                    async init() {
                        const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('loading-indicator', 'tests/indicators')))
                        if (this.isDestroyed) return
                        this.renderer = mount(this.$refs.host, @js($framework), @js($this->getIndicatorCases()))
                    },
                    destroy() {
                        this.isDestroyed = true
                        this.renderer?.destroy()
                    },
                }"
            >
                <h2>{{ ucfirst($framework) }}</h2>
                <div
                    wire:ignore
                    x-ref="host"
                    data-indicator-row="{{ $framework }}"
                    role="status"
                    aria-label="Loading content"
                ></div>
                <x-filament::button
                    color="gray"
                    data-testid="update-{{ $framework }}"
                    x-on:click="renderer.update()"
                >
                    Update {{ ucfirst($framework) }} indicators
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    data-testid="reset-{{ $framework }}"
                    x-on:click="renderer.reset()"
                >
                    Reset {{ ucfirst($framework) }} indicators
                </x-filament::button>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
