@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\Support\HtmlString;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <style>
        [data-fieldset-comparison] {
            display: grid;
            gap: 24px;
            max-width: 32rem;
        }
        [data-fieldset-row] {
            display: grid;
            gap: 12px;
            margin-block: 12px;
        }
    </style>
    <form id="profile"></form>
    <div data-fieldset-comparison>
        <section>
            <h2>Blade</h2>
            <div data-fieldset-row="blade">
                @foreach ($this->getFieldsetCases() as $attributes)
                    @php
                        if ($attributes['rich'] ?? false) {
                            $attributes['label'] = new HtmlString('<strong>Address</strong>');
                        }
                        unset($attributes['rich']);
                    @endphp

                    <div>
                        <x-filament::fieldset
                            :attributes="new ComponentAttributeBag($attributes)"
                        >
                            <input aria-label="Street" name="street" />
                        </x-filament::fieldset>
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
                        const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('fieldset', 'tests/fieldset')))
                        if (this.isDestroyed) return
                        this.renderer = mount(this.$refs.host, @js($framework), @js($this->getFieldsetCases()))
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
                    data-fieldset-row="{{ $framework }}"
                ></div>
                <x-filament::button
                    color="gray"
                    data-testid="update-{{ $framework }}"
                    x-on:click="renderer.update()"
                >
                    Update {{ ucfirst($framework) }} fieldset
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    data-testid="reset-{{ $framework }}"
                    x-on:click="renderer.reset()"
                >
                    Reset {{ ucfirst($framework) }} fieldset
                </x-filament::button>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
