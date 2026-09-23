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
            <x-filament::button
                data-testid="text-{{ $framework }}"
                x-on:click="renderer.update({ type: 'text' })"
            >
                Text type
            </x-filament::button>
            <x-filament::button
                data-testid="number-{{ $framework }}"
                x-on:click="renderer.update({ type: 'number' })"
            >
                Number type
            </x-filament::button>
            @if ($framework === 'vue')
                <x-filament::button
                    data-testid="number-modifier"
                    x-on:click="renderer.update({ type: 'text', modifiers: { number: true } })"
                >
                    Number modifier
                </x-filament::button>
                <x-filament::button
                    data-testid="trim-modifier"
                    x-on:click="renderer.update({ type: 'text', modifiers: { trim: true } })"
                >
                    Trim modifier
                </x-filament::button>
            @endif
        </section>
    @endforeach
</x-filament-panels::page>
