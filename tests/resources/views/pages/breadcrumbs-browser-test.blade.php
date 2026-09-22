@php
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Facades\FilamentIcon;
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\SupportIconAlias;
    use Illuminate\Support\HtmlString;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <style>
        [data-breadcrumbs-comparison] {
            display: grid;
            gap: 24px;
            max-width: 32rem;
            padding: 16px;
        }
        [data-breadcrumbs-row] {
            display: grid;
            gap: 12px;
            margin-block: 12px;
        }
    </style>

    <div data-breadcrumbs-comparison>
        <section>
            <h2>Blade</h2>
            <div data-breadcrumbs-row="blade">
                @foreach ($this->getBreadcrumbCases() as $attributes)
                    @php
                        $breadcrumbs = [];
                        foreach ($attributes['breadcrumbs'] ?? [] as $item) {
                            if (isset($item['href'])) {
                                $breadcrumbs[$item['href']] = $item['label'];
                            } else {
                                $breadcrumbs[] = $item['label'];
                            }
                        }
                        if ($attributes['custom'] ?? false) {
                            FilamentIcon::register([
                                SupportIconAlias::BREADCRUMBS_SEPARATOR => new HtmlString('/'),
                                SupportIconAlias::BREADCRUMBS_SEPARATOR_RTL => new HtmlString(chr(92)),
                            ]);
                        }
                        unset($attributes['breadcrumbs'], $attributes['custom']);
                    @endphp

                    <div>
                        <x-filament::breadcrumbs
                            :breadcrumbs="$breadcrumbs"
                            :attributes="new ComponentAttributeBag($attributes)"
                        />
                    </div>
                @endforeach

                @php
                    FilamentIcon::register([
                        SupportIconAlias::BREADCRUMBS_SEPARATOR => Heroicon::ChevronRight,
                        SupportIconAlias::BREADCRUMBS_SEPARATOR_RTL => Heroicon::ChevronLeft,
                    ]);
                @endphp
            </div>
        </section>
        @foreach (['react', 'vue', 'svelte'] as $framework)
            <section
                x-data="{
                    renderer: null,
                    isDestroyed: false,
                    async init() {
                        const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('breadcrumbs', 'tests/breadcrumbs')))
                        if (this.isDestroyed) return
                        this.renderer = mount(this.$refs.host, @js($framework), @js($this->getBreadcrumbCases()))
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
                    data-breadcrumbs-row="{{ $framework }}"
                ></div>
                <x-filament::button
                    color="gray"
                    data-testid="update-{{ $framework }}"
                    x-on:click="renderer.update()"
                >
                    Update {{ ucfirst($framework) }} breadcrumbs
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    data-testid="reset-{{ $framework }}"
                    x-on:click="renderer.reset()"
                >
                    Reset {{ ucfirst($framework) }} breadcrumbs
                </x-filament::button>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
