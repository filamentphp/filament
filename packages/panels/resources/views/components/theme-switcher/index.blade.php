<div
    x-data="{ theme: null, storageKey: 'theme-{{ filament()->getId() }}' }"
    x-init="
        $watch('theme', () => {
            $dispatch('theme-changed', theme)
        })

        theme = localStorage.getItem(storageKey) || localStorage.getItem('theme') || @js(filament()->getDefaultThemeMode()->value)
    "
    class="fi-theme-switcher"
>
    <x-filament-panels::theme-switcher.button
        :icon="\Filament\Support\Icons\Heroicon::Sun"
        theme="light"
    />

    <x-filament-panels::theme-switcher.button
        :icon="\Filament\Support\Icons\Heroicon::Moon"
        theme="dark"
    />

    <x-filament-panels::theme-switcher.button
        :icon="\Filament\Support\Icons\Heroicon::ComputerDesktop"
        theme="system"
    />
</div>
