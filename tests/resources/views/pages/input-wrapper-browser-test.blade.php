@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\Support\HtmlString;

    $icon = new HtmlString('<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h16"></path></svg>');
@endphp

<x-filament-panels::page>
    <section>
        <h2>Blade</h2>
        <div data-wrapper-row="blade">
            @foreach ($this->getWrapperCases() as $configuration)
                <div>
                    <x-filament::input.wrapper
                        :disabled="$configuration['disabled'] ?? false"
                        :valid="$configuration['valid'] ?? true"
                        :inline-prefix="$configuration['inlinePrefix'] ?? false"
                        :inline-suffix="$configuration['inlineSuffix'] ?? false"
                        :prefix="($configuration['rich'] ?? false) ? new HtmlString('<strong>Price</strong>') : ($configuration['prefix'] ?? null)"
                        :suffix="$configuration['suffix'] ?? null"
                        :prefix-icon="($configuration['icons'] ?? false) ? $icon : null"
                        :suffix-icon="($configuration['icons'] ?? false) ? $icon : null"
                    >
                        <input class="fi-input" aria-label="Amount" required />
                    </x-filament::input.wrapper>
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
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('input-wrapper', 'tests/input-wrappers')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework), @js($this->getWrapperCases()))
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
                data-wrapper-row="{{ $framework }}"
            ></div>
            @foreach (['update', 'clear', 'reset'] as $action)
                <x-filament::button
                    color="gray"
                    data-testid="{{ $action }}-{{ $framework }}"
                    x-on:click="renderer.{{ $action }}()"
                >
                    {{ ucfirst($action) }} {{ ucfirst($framework) }} wrappers
                </x-filament::button>
            @endforeach

            <x-filament::button
                data-testid="currency-{{ $framework }}"
                x-on:click="renderer.update('USD')"
            >
                Change currency
            </x-filament::button>
        </section>
    @endforeach
</x-filament-panels::page>
