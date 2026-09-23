@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <section>
        <h2>Blade</h2>
        <div data-loading-section-row="blade">
            @foreach ($this->getLoadingSectionCases() as $attributes)
                <div
                    class="fi-grid"
                    style="
                        --cols-default: repeat(4, minmax(0, 1fr));
                        container-type: inline-size;
                    "
                >
                    <x-filament::loading-section
                        :attributes="new ComponentAttributeBag($attributes)"
                    />
                </div>
            @endforeach
        </div>
    </section>
    @foreach (['react', 'vue', 'svelte'] as $framework)
        <section
            x-data="{
                renderer: null,
                isDestroyed: false,
                async init() {
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('loading-section', 'tests/loading-section')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework), @js($this->getLoadingSectionCases()))
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
                data-loading-section-row="{{ $framework }}"
            ></div>
        </section>
    @endforeach
</x-filament-panels::page>
