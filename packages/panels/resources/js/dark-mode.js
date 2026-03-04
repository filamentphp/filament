document.addEventListener('alpine:init', () => {
    const panelId = getComputedStyle(document.documentElement)
        .getPropertyValue('--panel-id')
        .trim()
        .replace(/['"]/g, '')

    const storageKey = panelId ? `theme-${panelId}` : 'theme'

    const theme =
        localStorage.getItem(storageKey) ??
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

        localStorage.setItem(storageKey, theme)

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
            if (localStorage.getItem(storageKey) === 'system') {
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
