export default () => ({
    isMobile: false,

    desktopMediaQuery: null,

    desktopMediaQueryListener: null,

    isOpen: window.Alpine.$persist(true).as('filament:sidebar:open'),
    isOpenDesktop: window.Alpine.$persist(true).as(
        'filament:sidebar:desktop_open',
    ),

    collapsedGroups: window.Alpine.$persist(null).as(
        'filament:sidebar:collapsed_groups',
    ),

    scrollTop: 0,

    previouslyFocusedElement: null,

    get isOpenMobile() {
        return this.isMobile && this.isOpen
    },

    init() {
        this.setUpMediaQuery()

        document.addEventListener('livewire:navigate', () => {
            const nav = document.querySelector(
                '.fi-main-sidebar .fi-sidebar-nav',
            )

            if (nav) {
                this.scrollTop = nav.scrollTop
            }
        })

        document.addEventListener('livewire:navigating', () => {
            if (this.isMobile) {
                this.close()
            }
        })

        document.addEventListener('livewire:navigated', () => {
            this.setUpMediaQuery()

            requestAnimationFrame(() => {
                const nav = document.querySelector(
                    '.fi-main-sidebar .fi-sidebar-nav',
                )

                if (nav && this.scrollTop) {
                    nav.scrollTop = this.scrollTop
                }
            })
        })
    },

    setUpMediaQuery() {
        this.desktopMediaQuery?.removeEventListener(
            'change',
            this.desktopMediaQueryListener,
        )

        const breakpoint = window
            .getComputedStyle(document.documentElement)
            .getPropertyValue('--breakpoint-lg')
            .trim()

        this.desktopMediaQuery = window.matchMedia(`(min-width: ${breakpoint})`)
        this.isMobile = !this.desktopMediaQuery.matches

        this.desktopMediaQueryListener = ({ matches }) => {
            this.isMobile = !matches

            if (this.isMobile) {
                this.isOpenDesktop = this.isOpen

                if (this.isOpen) {
                    this.close()
                }
            } else {
                this.previouslyFocusedElement = null
                this.isOpen = this.isOpenDesktop
            }
        }

        this.desktopMediaQuery.addEventListener(
            'change',
            this.desktopMediaQueryListener,
        )

        if (this.isMobile) {
            if (this.isOpen) {
                this.isOpenDesktop = true
                this.close()
            }
        } else {
            this.isOpen = this.isOpenDesktop
        }
    },

    groupIsCollapsed(group) {
        return this.collapsedGroups.includes(group)
    },

    collapseGroup(group) {
        if (this.collapsedGroups.includes(group)) {
            return
        }

        this.collapsedGroups = this.collapsedGroups.concat(group)
    },

    toggleCollapsedGroup(group) {
        this.collapsedGroups = this.collapsedGroups.includes(group)
            ? this.collapsedGroups.filter(
                  (collapsedGroup) => collapsedGroup !== group,
              )
            : this.collapsedGroups.concat(group)
    },

    close() {
        this.isOpen = false

        const previouslyFocusedElement = this.previouslyFocusedElement

        this.previouslyFocusedElement = null

        if (previouslyFocusedElement) {
            window.Alpine.nextTick(() => {
                requestAnimationFrame(() => {
                    if (
                        this.isMobile &&
                        !this.isOpen &&
                        previouslyFocusedElement.isConnected
                    ) {
                        previouslyFocusedElement.focus({ preventScroll: true })
                    }
                })
            })
        }

        if (!this.isMobile) {
            this.isOpenDesktop = false
        }
    },

    open(trigger = document.activeElement) {
        if (this.isMobile && !this.isOpen) {
            this.previouslyFocusedElement = trigger
        }

        this.isOpen = true

        if (!this.isMobile) {
            this.isOpenDesktop = true
        }
    },
})
