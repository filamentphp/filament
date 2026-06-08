const scrollPositions = new Map()

let isHistoryNavigation = false

document.addEventListener('livewire:navigate', (event) => {
    scrollPositions.set(window.location.href, {
        x: window.scrollX,
        y: window.scrollY,
    })

    isHistoryNavigation = event.detail?.history ?? false
})

document.addEventListener('livewire:navigated', () => {
    if (!isHistoryNavigation) {
        return
    }

    const position = scrollPositions.get(window.location.href)

    if (!position) {
        return
    }

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            window.scrollTo({
                top: position.y,
                left: position.x,
                behavior: 'instant',
            })
        })
    })
})
