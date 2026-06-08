const scrollPositions = new Map()

let pendingHistoryRestoreKey = null

function getPageKey() {
    return window.location.pathname + window.location.search
}

function getPageKeyFromUrl(url) {
    return url.pathname + url.search
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
    setTimeout(apply, 150)
}

document.addEventListener('livewire:navigate', (event) => {
    if (event.detail?.history) {
        pendingHistoryRestoreKey = getPageKeyFromUrl(event.detail.url)

        return
    }

    saveCurrentPageScrollPosition()
    pendingHistoryRestoreKey = null
})

document.addEventListener('livewire:navigating', (event) => {
    if (!pendingHistoryRestoreKey) {
        return
    }

    const restoreKey = pendingHistoryRestoreKey
    const position = scrollPositions.get(restoreKey)

    if (!position) {
        return
    }

    event.detail.onSwap(() => {
        restorePageScrollPosition(position)
    })
})

document.addEventListener('livewire:navigated', () => {
    if (!pendingHistoryRestoreKey) {
        return
    }

    const position = scrollPositions.get(pendingHistoryRestoreKey)

    if (position) {
        restorePageScrollPosition(position)
    }

    pendingHistoryRestoreKey = null
})
