@php
    use Filament\Support\Facades\FilamentAsset;
@endphp

<x-filament-panels::page>
    <section data-input-row="blade">
        <h2>Blade</h2>
        <x-filament::input.wrapper prefix="£">
            <x-filament::input
                type="number"
                name="amount"
                aria-label="Amount"
                min="0"
                required
                value="0"
            />
        </x-filament::input.wrapper>
    </section>
    @foreach (['react', 'vue', 'svelte'] as $framework)
        <section
            x-data="{
                renderer: null,
                isDestroyed: false,
                async init() {
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('input', 'tests/inputs')))
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
                data-input-row="{{ $framework }}"
            ></div>
            <x-filament::button
                data-testid="disabled-{{ $framework }}"
                x-on:click="renderer.update({ disabled: true, inline: true })"
            >
                Disable
            </x-filament::button>
            <x-filament::button
                data-testid="readonly-{{ $framework }}"
                x-on:click="renderer.update({ readOnly: true, inline: true })"
            >
                Read only
            </x-filament::button>
            <x-filament::button
                data-testid="enabled-{{ $framework }}"
                x-on:click="renderer.update({})"
            >
                Enable
            </x-filament::button>
        </section>
    @endforeach
</x-filament-panels::page>
