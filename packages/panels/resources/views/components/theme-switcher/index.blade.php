<div
    x-data="{
        theme: null,

        init: function () {
            this.theme = localStorage.getItem('theme') || 'system'

            $dispatch('theme-changed', theme)

            $watch('theme', (theme) => {
                $dispatch('theme-changed', theme)
            })
        },
    }"
    class="fi-theme-switcher grid grid-flow-col gap-x-1"
>
    <x-filament-panels::theme-switcher.button
        icon="heroicon-m-sun"
        theme="light"
    />

    <x-filament-panels::theme-switcher.button
        icon="heroicon-m-moon"
        theme="dark"
    />

    <x-filament-panels::theme-switcher.button
        icon="heroicon-m-computer-desktop"
        theme="system"
    />
</div>
