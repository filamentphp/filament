export default () => ({
    theme: null,

    switchTheme(theme) {
        this.theme = theme

        window.localStorage.setItem('theme', theme)

        if (theme !== 'system') {
            this.setDocumentClass(theme)
        } else {
            this.setDocumentClass(this.browserTheme())
        }
    },

    browserTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    },

    setDocumentClass(theme) {
        if (theme === 'dark') {
            if (! document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.add('dark');
            }
        } else {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
            }
        }
    },

    registerListener() {
        window.matchMedia('(prefers-color-scheme: dark)')
            .addEventListener('change', () => {
                if (!this.theme) {
                    this.setDocumentClass(this.browserTheme());
                }
            })
    },

    storedTheme() {
        return window.localStorage.getItem('theme')
    },

    loadStoredTheme() {
        if (this.storedTheme()) {
            this.switchTheme(this.storedTheme())
        } else {
            this.switchTheme(this.browserTheme())
        }
    },

    init() {
        this.loadStoredTheme()
        this.registerListener()
    }
})
