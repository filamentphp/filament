@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <section>
        <h2>Blade</h2>
        <form id="radio-form-blade"></form>
        <div data-radio-row="blade">
            @foreach ($this->getRadioCases() as $attributes)
                <label>
                    <x-filament::input.radio
                        :attributes="new ComponentAttributeBag(array_diff_key($attributes, ['defaultChecked' => true]))"
                        :checked="$attributes['defaultChecked'] ?? false"
                        form="radio-form-blade"
                    />
                    {{ $attributes['name'] }} {{ $attributes['value'] ?? '' }}
                </label>
            @endforeach

            @foreach (['standard', 'express'] as $value)
                <label>
                    <x-filament::input.radio
                        name="controlled"
                        :value="$value"
                        :checked="$value === 'standard'"
                        form="radio-form-blade"
                    />
                    {{ $value }}
                </label>
            @endforeach
        </div>
    </section>
    @foreach (['react', 'vue', 'svelte'] as $framework)
        <section
            x-data="{
                renderer: null,
                isDestroyed: false,
                async init() {
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('radio', 'tests/radios')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework), @js($this->getRadioCases()))
                },
                destroy() {
                    this.isDestroyed = true
                    this.renderer?.destroy()
                },
            }"
        >
            <h2>{{ ucfirst($framework) }}</h2>
            <form
                id="radio-form-{{ $framework }}"
                x-on:reset="renderer.reset()"
            ></form>
            <div
                wire:ignore
                x-ref="host"
                data-radio-row="{{ $framework }}"
            ></div>
            <x-filament::button
                color="gray"
                data-testid="invalidate-{{ $framework }}"
                x-on:click="renderer.invalidate()"
            >
                Show errors
            </x-filament::button>
            <x-filament::button
                color="gray"
                data-testid="select-{{ $framework }}"
                x-on:click="renderer.select()"
            >
                Select express
            </x-filament::button>
            <x-filament::button
                color="gray"
                data-testid="clear-{{ $framework }}"
                x-on:click="renderer.clear()"
            >
                Clear selection
            </x-filament::button>
            @if ($framework !== 'react')
                <x-filament::button
                    color="gray"
                    data-testid="release-{{ $framework }}"
                    x-on:click="renderer.release()"
                >
                    Use undefined model
                </x-filament::button>
            @endif

            <x-filament::button
                color="gray"
                data-testid="reset-{{ $framework }}"
                type="reset"
                form-id="radio-form-{{ $framework }}"
            >
                Reset form
            </x-filament::button>
        </section>
    @endforeach
</x-filament-panels::page>
