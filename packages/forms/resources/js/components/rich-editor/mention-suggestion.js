import { computePosition, flip, shift } from '@floating-ui/dom'

const updatePosition = (editor, element) => {
    const referenceElement = {
        getBoundingClientRect: () => {
            const { from, to } = editor.state.selection
            const start = editor.view.coordsAtPos(from)
            const end = editor.view.coordsAtPos(to)

            return {
                top: Math.min(start.top, end.top),
                bottom: Math.max(start.bottom, end.bottom),
                left: Math.min(start.left, end.left),
                right: Math.max(start.right, end.right),
                width: Math.abs(end.right - start.left),
                height: Math.abs(end.bottom - start.top),
                x: Math.min(start.left, end.left),
                y: Math.min(start.top, end.top),
            }
        },
    }

    computePosition(referenceElement, element, {
        placement: 'bottom-start',
        strategy: 'absolute',
        middleware: [shift(), flip()],
    }).then(({ x, y, strategy }) => {
        element.style.width = 'max-content'
        element.style.position = strategy
        element.style.left = `${x}px`
        element.style.top = `${y}px`
    })
}

export default ({
    items = [],
    noOptionsMessage = null,
    noSearchResultsMessage = null,
    searchPrompt = null,
    searchingMessage = null,
    isSearchable = false,
}) => {
    let renderContext = null

    return {
        items: async ({ query }) => {
            if (typeof items === 'function') {
                if (renderContext && isSearchable) {
                    renderContext.setLoading(true)
                }

                try {
                    const result = items({ query })
                    const resolved = Array.isArray(result)
                        ? result
                        : await result

                    if (renderContext) {
                        renderContext.setLoading(false)
                    }

                    return resolved
                } catch {
                    if (renderContext) {
                        renderContext.setLoading(false)
                    }

                    return []
                }
            }

            if (!query) return items

            const searchQuery = String(query).toLowerCase()
            return items.filter((item) => {
                const label =
                    typeof item === 'string'
                        ? item
                        : (item?.label ?? item?.name ?? '')
                return String(label).toLowerCase().includes(searchQuery)
            })
        },

        render: () => {
            let element
            let selectedIndex = 0
            let currentProps = null
            let isLoading = false

            renderContext = {
                setLoading: (loading) => {
                    isLoading = loading
                    renderItems()
                },
            }

            const createDropdown = () => {
                const dropdown = document.createElement('div')
                dropdown.className =
                    'fi-dropdown-panel fi-dropdown-list fi-scrollable'
                dropdown.style.maxHeight = '15rem'
                dropdown.style.minWidth = '12rem'

                return dropdown
            }

            const renderItems = () => {
                if (!element || !currentProps) return

                const items = Array.isArray(currentProps.items)
                    ? currentProps.items
                    : []
                const query = currentProps.query ?? ''

                element.innerHTML = ''

                if (isLoading) {
                    const message = searchingMessage ?? 'Searching...'
                    const messageElement = document.createElement('div')
                    messageElement.className = 'fi-dropdown-header'

                    const messageSpan = document.createElement('span')
                    messageSpan.style.whiteSpace = 'normal'
                    messageSpan.textContent = message
                    messageElement.appendChild(messageSpan)

                    element.appendChild(messageElement)
                    return
                }

                if (items.length) {
                    items.forEach((raw, index) => {
                        const label =
                            typeof raw === 'string'
                                ? raw
                                : (raw?.label ??
                                  raw?.name ??
                                  String(raw?.id ?? ''))
                        const id =
                            typeof raw === 'object' ? (raw?.id ?? label) : label

                        const button = document.createElement('button')
                        button.className = `fi-dropdown-list-item ${index === selectedIndex ? 'fi-selected' : ''}`
                        button.type = 'button'
                        button.addEventListener('click', () =>
                            selectItem(id, label),
                        )

                        const labelSpan = document.createElement('span')
                        labelSpan.className = 'fi-dropdown-list-item-label'
                        labelSpan.textContent = label
                        button.appendChild(labelSpan)

                        element.appendChild(button)
                    })
                } else {
                    const message = getEmptyMessage(query)

                    if (message) {
                        const messageElement = document.createElement('div')
                        messageElement.className = 'fi-dropdown-header'

                        const messageSpan = document.createElement('span')
                        messageSpan.style.whiteSpace = 'normal'
                        messageSpan.textContent = message
                        messageElement.appendChild(messageSpan)

                        element.appendChild(messageElement)
                    }
                }
            }

            const getEmptyMessage = (query) => {
                if (query) {
                    return noSearchResultsMessage
                }

                if (isSearchable) {
                    return searchPrompt
                }

                return noOptionsMessage
            }

            const selectItem = (id, label) => {
                if (!currentProps) return

                currentProps.command({ id, label })
            }

            const scrollToSelected = () => {
                if (!element || !currentProps) return

                const items = currentProps.items || []
                if (items.length === 0) return

                const selectedButton = element.children[selectedIndex]

                if (selectedButton) {
                    const rect = selectedButton.getBoundingClientRect()
                    const containerRect = element.getBoundingClientRect()
                    if (
                        rect.top < containerRect.top ||
                        rect.bottom > containerRect.bottom
                    ) {
                        selectedButton.scrollIntoView({ block: 'nearest' })
                    }
                }
            }

            const upHandler = () => {
                if (!currentProps) return

                const items = Array.isArray(currentProps.items)
                    ? currentProps.items
                    : []
                if (items.length === 0) return

                selectedIndex =
                    (selectedIndex + items.length - 1) % items.length
                renderItems()
                scrollToSelected()
            }

            const downHandler = () => {
                if (!currentProps) return

                const items = currentProps.items || []
                if (items.length === 0) return

                selectedIndex = (selectedIndex + 1) % items.length
                renderItems()
                scrollToSelected()
            }

            const enterHandler = () => {
                const items = currentProps?.items || []
                if (items.length === 0) return

                const raw = items[selectedIndex]
                const label =
                    typeof raw === 'string'
                        ? raw
                        : (raw?.label ?? raw?.name ?? String(raw?.id ?? ''))
                const id = typeof raw === 'object' ? (raw?.id ?? label) : label
                selectItem(id, label)
            }

            return {
                onStart: (props) => {
                    currentProps = props
                    selectedIndex = 0

                    element = createDropdown()
                    element.style.position = 'absolute'

                    renderItems()

                    document.body.appendChild(element)

                    if (!props.clientRect) {
                        return
                    }

                    updatePosition(props.editor, element)
                },

                onUpdate: (props) => {
                    currentProps = props
                    selectedIndex = 0

                    renderItems()
                    scrollToSelected()

                    if (!props.clientRect) {
                        return
                    }

                    updatePosition(props.editor, element)
                },

                onKeyDown: (props) => {
                    if (props.event.key === 'Escape') {
                        if (element && element.parentNode) {
                            element.parentNode.removeChild(element)
                        }

                        return true
                    }

                    if (props.event.key === 'ArrowUp') {
                        upHandler()
                        return true
                    }

                    if (props.event.key === 'ArrowDown') {
                        downHandler()
                        return true
                    }

                    if (props.event.key === 'Enter') {
                        enterHandler()
                        return true
                    }

                    return false
                },

                onExit: () => {
                    if (element && element.parentNode) {
                        element.parentNode.removeChild(element)
                    }

                    renderContext = null
                },
            }
        },
    }
}
