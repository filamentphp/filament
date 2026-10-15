const breakpoint = 1024

export default () => ({
    isOpen: window.Alpine.$persist(true).as('filament:sidebar:open'),
    isOpenDesktop: window.Alpine.$persist(true).as(
        'filament:sidebar:desktop_open',
    ),
    isMobile:
        typeof window !== 'undefined' ? window.innerWidth < breakpoint : false,
    previouslyFocusedElement: null,

    collapsedGroups: window.Alpine.$persist(null).as(
        'filament:sidebar:collapsed_groups',
    ),

    scrollTop: 0,

    init() {
        this.resizeObserver = null

        this.setUpResizeObserver()

        document.addEventListener('livewire:navigate', () => {
            const nav = document.querySelector(
                '.fi-main-sidebar .fi-sidebar-nav',
            )

            if (nav) {
                this.scrollTop = nav.scrollTop
            }

            if (this.isMobile && this.isOpen) {
                this.close()
            }
        })

        document.addEventListener('livewire:navigated', () => {
            this.setUpResizeObserver()

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

    setUpResizeObserver() {
        if (this.resizeObserver) {
            this.resizeObserver.disconnect()
        }

        let previousWidth = window.innerWidth

        this.resizeObserver = new ResizeObserver(() => {
            const currentWidth = window.innerWidth
            const wasDesktop = previousWidth >= breakpoint
            const isMobile = currentWidth < breakpoint
            const isDesktop = currentWidth >= breakpoint

            this.isMobile = isMobile

            // Resize desktop to mobile
            if (wasDesktop && isMobile) {
                this.isOpenDesktop = this.isOpen

                if (this.isOpen) {
                    this.close()
                }
            }
            // Resize mobile to desktop
            else if (!wasDesktop && isDesktop) {
                this.isOpen = this.isOpenDesktop
            }

            previousWidth = currentWidth
        })

        this.resizeObserver.observe(document.body)

        this.isMobile = window.innerWidth < breakpoint

        if (window.innerWidth < breakpoint) {
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

        if (window.innerWidth >= breakpoint) {
            this.isOpenDesktop = false
        }

        if (this.isMobile) {
            requestAnimationFrame(() => {
                const elementToFocus = this.previouslyFocusedElement
                    ?.isConnected
                    ? this.previouslyFocusedElement
                    : document.querySelector(
                          '.fi-topbar-open-sidebar-btn, .fi-layout-sidebar-toggle-btn',
                      )

                elementToFocus?.focus()
                this.previouslyFocusedElement = null
            })
        }
    },

    open() {
        if (this.isMobile) {
            this.previouslyFocusedElement = document.activeElement
        }

        this.isOpen = true

        if (window.innerWidth >= breakpoint) {
            this.isOpenDesktop = true
        }

        if (this.isMobile) {
            requestAnimationFrame(() => {
                const sidebar = document.querySelector('.fi-main-sidebar')

                if (!sidebar) {
                    return
                }

                const closeButton = sidebar.querySelector(
                    '.fi-sidebar-close-btn',
                )
                const activeItem = sidebar.querySelector(
                    '.fi-sidebar-item.fi-active .fi-sidebar-item-btn, .fi-sidebar-item.fi-active a',
                )
                const firstFocusable = sidebar.querySelector(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
                )

                const target = closeButton ?? activeItem ?? firstFocusable

                target?.focus()
            })
        }
    },
})
