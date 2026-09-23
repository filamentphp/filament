@php
    use Filament\Support\Facades\FilamentAsset;
@endphp

<x-filament-panels::page>
    <section data-select-row="blade">
        <h2>Blade</h2>
        <x-filament::input.wrapper prefix="Art">
            <x-filament::input.select
                name="workshop"
                aria-label="workshop"
                required
            >
                <option value="">Choose a workshop</option>
                <option value="drawing" selected>drawing</option>
                <option value="ceramics">ceramics</option>
                <option value="archived" disabled>archived</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </section>
    @foreach (['react', 'vue', 'svelte'] as $framework)
        <section
            x-data="{
                renderer: null,
                isDestroyed: false,
                async init() {
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('select', 'tests/selects')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework))
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
                data-select-row="{{ $framework }}"
            ></div>
            <x-filament::button
                data-testid="disabled-{{ $framework }}"
                x-on:click="renderer.update({ disabled: true, inline: true })"
            >
                Disable
            </x-filament::button>
            <x-filament::button
                data-testid="enabled-{{ $framework }}"
                x-on:click="renderer.update({})"
            >
                Restore options and enable
            </x-filament::button>
            <x-filament::button
                data-testid="remove-{{ $framework }}"
                x-on:click="renderer.update({ remove: true })"
            >
                Remove drawing
            </x-filament::button>
        </section>
    @endforeach
</x-filament-panels::page>
