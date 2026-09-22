@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <div data-checkbox-comparison>
        <section>
            <h2>Blade</h2>
            <form id="checkbox-form-blade"></form>
            <div data-checkbox-row="blade">
                @foreach ($this->getCheckboxCases() as $attributes)
                    <label>
                        <x-filament::input.checkbox
                            :attributes="new ComponentAttributeBag(array_diff_key($attributes, ['defaultChecked' => true, 'indeterminate' => true]))"
                            :checked="$attributes['defaultChecked'] ?? false"
                            x-init="$el.indeterminate = {{ ($attributes['indeterminate'] ?? false) ? 'true' : 'false' }}"
                            form="checkbox-form-blade"
                        />
                        {{ $attributes['name'] }}
                    </label>
                @endforeach

                <label>
                    <x-filament::input.checkbox
                        name="controlled"
                        value="enabled"
                        form="checkbox-form-blade"
                    />
                    Controlled
                </label>
            </div>
        </section>
        @foreach (['react', 'vue', 'svelte'] as $framework)
            <section
                x-data="{
                    renderer: null,
                    isDestroyed: false,
                    async init() {
                        const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('checkbox', 'tests/checkboxes')))
                        if (this.isDestroyed) return
                        this.renderer = mount(this.$refs.host, @js($framework), @js($this->getCheckboxCases()))
                    },
                    destroy() {
                        this.isDestroyed = true
                        this.renderer?.destroy()
                    },
                }"
            >
                <h2>{{ ucfirst($framework) }}</h2>
                <form id="checkbox-form-{{ $framework }}"></form>
                <div
                    wire:ignore
                    x-ref="host"
                    data-checkbox-row="{{ $framework }}"
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
                    data-testid="toggle-{{ $framework }}"
                    x-on:click="renderer.toggle()"
                >
                    Toggle controlled value
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    data-testid="reset-{{ $framework }}"
                    x-on:click="document.getElementById('checkbox-form-{{ $framework }}').reset()"
                >
                    Reset form
                </x-filament::button>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
