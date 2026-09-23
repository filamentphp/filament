@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <div id="projects">Projects</div>
    <section>
        <h2>Blade</h2>
        <div data-actions-row="blade">
            @foreach ($this->getActionsCases() as $attributes)
                <form>
                    <x-filament::actions
                        :attributes="new ComponentAttributeBag($attributes)"
                    >
                        <input aria-label="Project name" name="project" />
                        <button type="submit">Save</button>
                        <button type="button" disabled>Unavailable</button>
                        <a href="#projects">Projects</a>
                    </x-filament::actions>
                </form>
            @endforeach
        </div>
    </section>
    @foreach (['react', 'vue', 'svelte'] as $framework)
        <section
            x-data="{
                renderer: null,
                isDestroyed: false,
                async init() {
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('actions', 'tests/actions')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework), @js($this->getActionsCases()))
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
                data-actions-row="{{ $framework }}"
            ></div>
        </section>
    @endforeach
</x-filament-panels::page>
