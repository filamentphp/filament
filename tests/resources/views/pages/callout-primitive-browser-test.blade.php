@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\Support\HtmlString;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <div id="projects">Projects</div>
    <section>
        <h2>Blade</h2>
        <div data-callout-row="blade">
            @foreach ($this->getCalloutCases() as $attributes)
                @php
                    if ($attributes['rich'] ?? false) {
                        $attributes['heading'] = new HtmlString('<strong>Projects</strong>');
                        $attributes['description'] = new HtmlString('<em>Start here</em>');
                    }
                    if ($attributes['withIcon'] ?? false) {
                        $attributes['icon'] = new HtmlString('<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 4h16v16H4z"></path></svg>');
                    }
                    if ($attributes['withActions'] ?? false) {
                        $attributes['footer'] = new HtmlString('<input aria-label="Project name" name="project"><button type="button">Create project</button><a href="#projects">Browse projects</a>');
                    }
                    foreach (['footer', 'controls'] as $slot) {
                        if (is_string($attributes[$slot] ?? null)) {
                            $attributes[$slot] = new HtmlString(e($attributes[$slot]));
                        }
                    }
                    unset($attributes['rich'], $attributes['withIcon'], $attributes['withActions']);
                @endphp

                <div>
                    <x-filament::callout
                        :attributes="new ComponentAttributeBag($attributes)"
                    />
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
                    const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('callout', 'tests/callout')))
                    if (this.isDestroyed) return
                    this.renderer = mount(this.$refs.host, @js($framework), @js($this->getCalloutCases()))
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
                data-callout-row="{{ $framework }}"
            ></div>
            @foreach (['update', 'remove', 'reset'] as $operation)
                <x-filament::button
                    data-testid="{{ $operation }}-{{ $framework }}"
                    x-on:click="renderer.{{ $operation }}()"
                >
                    {{ ucfirst($operation) }} {{ $framework }}
                </x-filament::button>
            @endforeach
        </section>
    @endforeach
</x-filament-panels::page>
