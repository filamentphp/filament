document.addEventListener('alpine:init', () => {
    const theme =
        localStorage.getItem('theme') ??
        getComputedStyle(document.documentElement).getPropertyValue(
            '--default-theme-mode',
        )

    window.Alpine.store(
        'theme',
        theme === 'dark' ||
            (theme === 'system' &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)
            ? 'dark'
            : 'light',
    )

    window.addEventListener('theme-changed', (event) => {
        let theme = event.detail

        localStorage.setItem('theme', theme)

        if (theme === 'system') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches
                ? 'dark'
                : 'light'
        }

        window.Alpine.store('theme', theme)
    })

    window
        .matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', (event) => {
            if (localStorage.getItem('theme') === 'system') {
                window.Alpine.store('theme', event.matches ? 'dark' : 'light')
            }
        })

    window.Alpine.effect(() => {
        const theme = window.Alpine.store('theme')

        theme === 'dark'
            ? document.documentElement.classList.add('dark')
            : document.documentElement.classList.remove('dark')
    })
})
