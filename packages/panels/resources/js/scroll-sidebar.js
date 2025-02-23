document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        let activeSidebarItem = document.querySelector(
            '.fi-main-sidebar .fi-sidebar-item.fi-active',
        )

        if (!activeSidebarItem || activeSidebarItem.offsetParent === null) {
            activeSidebarItem = document.querySelector(
                '.fi-main-sidebar .fi-sidebar-group.fi-active',
            )
        }

        if (!activeSidebarItem || activeSidebarItem.offsetParent === null) {
            return
        }

        const sidebarWrapper = document.querySelector(
            '.fi-main-sidebar .fi-sidebar-nav',
        )

        if (!sidebarWrapper) {
            return
        }

        sidebarWrapper.scrollTo(
            0,
            activeSidebarItem.offsetTop - window.innerHeight / 2,
        )
    }, 10)
})
