@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\Support\HtmlString;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <section>
        <h2>Blade</h2>
        <div data-icon-row="blade">
            @foreach ($this->getIconCases() as $attributes)
                <div>
                    {{ \Filament\Support\generate_icon_html(($attributes['empty'] ?? false) ? null : ($attributes['src'] ?? new HtmlString('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 12h16"></path></svg>')), attributes: new ComponentAttributeBag(array_diff_key($attributes, ['size' => true, 'src' => true, 'empty' => true])), size: isset($attributes['size']) ? IconSize::from($attributes['size']) : null) }}
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
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('icon', 'tests/icons')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework), @js($this->getIconCases()))
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
                data-icon-row="{{ $framework }}"
            ></div>
            @foreach (['update', 'image', 'reset'] as $action)
                <x-filament::button
                    color="gray"
                    data-testid="{{ $action }}-{{ $framework }}"
                    x-on:click="renderer.{{ $action }}()"
                >
                    {{ ucfirst($action) }} {{ ucfirst($framework) }} icons
                </x-filament::button>
            @endforeach
        </section>
    @endforeach
</x-filament-panels::page>
