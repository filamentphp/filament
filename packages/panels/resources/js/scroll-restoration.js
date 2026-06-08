const scrollPositions = new Map()

let isHistoryNavigation = false

function getPageKey() {
    return window.location.pathname + window.location.search
}

function getPageScrollPosition() {
    return {
        x: window.scrollX || document.documentElement.scrollLeft || 0,
        y: window.scrollY || document.documentElement.scrollTop || 0,
    }
}

function saveCurrentPageScrollPosition() {
    scrollPositions.set(getPageKey(), getPageScrollPosition())
}

function restorePageScrollPosition(position) {
    const apply = () => {
        window.scrollTo({
            top: position.y,
            left: position.x,
            behavior: 'instant',
        })
    }

    apply()

    requestAnimationFrame(() => {
        apply()

        requestAnimationFrame(apply)
    })

    setTimeout(apply, 0)
    setTimeout(apply, 50)
}

window.addEventListener('popstate', () => {
    isHistoryNavigation = true
})

document.addEventListener('livewire:navigate', (event) => {
    saveCurrentPageScrollPosition()

    isHistoryNavigation = event.detail?.history ?? isHistoryNavigation
})

document.addEventListener('livewire:navigated', () => {
    if (!isHistoryNavigation) {
        return
    }

    isHistoryNavigation = false

    const position = scrollPositions.get(getPageKey())

    if (!position) {
        return
    }

    restorePageScrollPosition(position)
})
