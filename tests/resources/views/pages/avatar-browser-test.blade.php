@php
    use Filament\Support\Facades\FilamentAsset;
    use Illuminate\View\ComponentAttributeBag;
@endphp

<x-filament-panels::page>
    <style>
        [data-avatar-comparison] {
            display: grid;
            gap: 24px;
        }
        [data-avatar-row] {
            display: flex;
            align-items: center;
            gap: 24px;
            min-height: 64px;
        }
        .avatar-custom-size {
            width: 56px;
            height: 48px;
        }
        .avatar-custom-theme {
            outline: 3px solid var(--primary-500);
        }
    </style>

    <div data-avatar-comparison>
        <section>
            <h2>Blade</h2>
            <div data-avatar-row="blade">
                @foreach ($this->getAvatarCases() as $attributes)
                    <x-filament::avatar
                        :attributes="new ComponentAttributeBag($attributes)"
                    />
                @endforeach
            </div>
        </section>

        @foreach (['react', 'vue', 'svelte'] as $framework)
            <section
                x-data="{
                    renderer: null,
                    isDestroyed: false,
                    async init() {
                        const { default: mount } = await import(@js(FilamentAsset::getScriptSrc('avatar', 'tests/avatars')))
                        if (this.isDestroyed) return
                        this.renderer = mount(this.$refs.host, @js($framework), @js($this->getAvatarCases()))
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
                    data-avatar-row="{{ $framework }}"
                ></div>
                <x-filament::button
                    color="gray"
                    data-testid="update-{{ $framework }}"
                    x-on:click="renderer.update()"
                >
                    Update {{ ucfirst($framework) }} avatars
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    data-testid="reset-{{ $framework }}"
                    x-on:click="renderer.reset()"
                >
                    Reset {{ ucfirst($framework) }} avatars
                </x-filament::button>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
