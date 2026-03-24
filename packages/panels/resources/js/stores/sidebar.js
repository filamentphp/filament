const breakpoint = 1024

export default () => ({
    isOpen: window.Alpine.$persist(true).as('isOpen'),
    isOpenDesktop: window.Alpine.$persist(true).as('isOpenDesktop'),

    collapsedGroups: window.Alpine.$persist(null).as('collapsedGroups'),

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
    },

    open() {
        this.isOpen = true

        if (window.innerWidth >= breakpoint) {
            this.isOpenDesktop = true
        }
    },
})
