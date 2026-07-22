const DESKTOP_MIN_WIDTH = 1024

let sidebar = null
let topbar = null
let observer = null
let frame = null
let lastOffset = null
let lastTop = null

const update = () => {
    if (!sidebar) {
        return
    }

    const offset = topbar?.offsetHeight ?? 0

    if (offset !== lastOffset) {
        document.body.style.setProperty(
            '--fi-sidebar-sticky-scroll-offset',
            `${offset}px`,
        )

        lastOffset = offset
    }

    if (window.innerWidth < DESKTOP_MIN_WIDTH) {
        if (lastTop !== null) {
            sidebar.style.removeProperty('top')

            lastTop = null
        }

        return
    }

    const available = window.innerHeight - offset
    const top =
        sidebar.offsetHeight <= available
            ? offset
            : window.innerHeight - sidebar.offsetHeight

    if (top !== lastTop) {
        sidebar.style.setProperty('top', `${top}px`, 'important')

        lastTop = top
    }
}

const scheduleUpdate = () => {
    if (frame || !sidebar) {
        return
    }

    frame = requestAnimationFrame(() => {
        frame = null
        update()
    })
}

const init = () => {
    observer?.disconnect()

    lastOffset = null
    lastTop = null

    sidebar = document.querySelector(
        '.fi-main-sidebar.fi-sidebar-sticky-scroll',
    )
    topbar = document.querySelector('.fi-topbar-ctn')

    if (!sidebar) {
        return
    }

    observer = new ResizeObserver(scheduleUpdate)
    observer.observe(sidebar)

    if (topbar) {
        observer.observe(topbar)
    }

    update()
}

window.addEventListener('resize', scheduleUpdate)
document.addEventListener('DOMContentLoaded', init)
document.addEventListener('livewire:navigated', init)
