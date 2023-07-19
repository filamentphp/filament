<div
    x-data="{
        theme: null,

        init: function () {
            this.theme = localStorage.getItem('theme') || 'system'

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', (event) => {
                    this.theme === 'dark' ||
                    (this.theme === 'system' && event.matches)
                        ? document.documentElement.classList.add('dark')
                        : document.documentElement.classList.remove('dark')
                })

            $watch('theme', (theme) => {
                localStorage.setItem('theme', theme)
                Alpine.store('theme', theme)

                theme === 'dark' ||
                (theme === 'system' &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches)
                    ? document.documentElement.classList.add('dark')
                    : document.documentElement.classList.remove('dark')
            })
        },

        isSystemDark: function () {
            return window.matchMedia('(prefers-color-scheme: dark)').matches
        },
    }"
    class="fi-theme-switcher grid grid-flow-col gap-x-1"
>
    <x-filament::theme-switcher.button icon="heroicon-m-sun" theme="light" />

    <x-filament::theme-switcher.button icon="heroicon-m-moon" theme="dark" />

    <x-filament::theme-switcher.button
        icon="heroicon-m-computer-desktop"
        theme="system"
    />
</div>
