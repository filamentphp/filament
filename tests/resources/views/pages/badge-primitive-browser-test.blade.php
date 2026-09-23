@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <form
        id="badge-form"
        x-data
        @submit.prevent="$el.dataset.submits = String(Number($el.dataset.submits || 0) + 1)"
    >
        <label>
            Shortcut focus target
            <input data-testid="shortcut-input" />
        </label>
    </form>
    <p id="badge-destination">Priority projects</p>
    <section>
        <h2>Blade</h2>
        <div data-badge-row="blade">
            @foreach ($this->getBadgeCases() as $attributes)
                <div>
                    <x-filament::badge
                        :attributes="new ComponentAttributeBag(collect($attributes)->except(['deletable', 'deleteLabel', 'form'])->all())"
                        :form-id="$attributes['form'] ?? null"
                    >
                        Priority
                        @if ($attributes['deletable'] ?? false)
                            <x-slot
                                name="deleteButton"
                                label="Remove priority"
                            ></x-slot>
                        @endif
                    </x-filament::badge>
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
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('badge', 'tests/badge')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework), @js($this->getBadgeCases()))
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
                data-badge-row="{{ $framework }}"
            ></div>
        </section>
    @endforeach
</x-filament-panels::page>
