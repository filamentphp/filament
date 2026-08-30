@php
    use Filament\Support\Icons\Heroicon;
@endphp

<div
    x-data="{ theme: null }"
    x-init="
        $watch('theme', () => {
            $dispatch('theme-changed', theme)
        })

        theme = localStorage.getItem('theme') || @js(filament()->getDefaultThemeMode()->value)
    "
    role="group"
    aria-label="{{ __('filament-panels::layout.actions.theme_switcher.label') }}"
    class="fi-theme-switcher"
>
    <x-filament-panels::theme-switcher.button
        :icon="Heroicon::Sun"
        theme="light"
    />

    <x-filament-panels::theme-switcher.button
        :icon="Heroicon::Moon"
        theme="dark"
    />

    <x-filament-panels::theme-switcher.button
        :icon="Heroicon::ComputerDesktop"
        theme="system"
    />
</div>
