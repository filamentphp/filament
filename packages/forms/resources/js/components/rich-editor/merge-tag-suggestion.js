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

export default ({ mergeTags, noMergeTagSearchResultsMessage }) => ({
    items: ({ query }) => {
        return Object.entries(mergeTags)
            .filter(
                ([id, label]) =>
                    id
                        .toLowerCase()
                        .replace(/\s/g, '')
                        .includes(query.toLowerCase()) ||
                    label
                        .toLowerCase()
                        .replace(/\s/g, '')
                        .includes(query.toLowerCase()),
            )
            .map(([id, label]) => ({ id, label }))
    },

    render: () => {
        let element
        let selectedIndex = 0
        let currentProps = null

        const createDropdown = () => {
            const dropdown = document.createElement('div')
            dropdown.className = 'fi-dropdown-panel fi-dropdown-list'
            dropdown.style.minWidth = '12rem'

            return dropdown
        }

        const renderItems = () => {
            if (!element || !currentProps) return

            const items = currentProps.items || []

            element.innerHTML = ''

            if (items.length) {
                items.forEach((item, index) => {
                    const button = document.createElement('button')
                    button.className = `fi-dropdown-list-item fi-dropdown-list-item-label ${index === selectedIndex ? 'fi-selected' : ''}`
                    button.textContent = item.label
                    button.type = 'button'
                    button.addEventListener('click', () => selectItem(index))
                    element.appendChild(button)
                })
            } else {
                const messageElement = document.createElement('div')
                messageElement.className = 'fi-dropdown-header'

                const messageSpan = document.createElement('span')
                messageSpan.style.whiteSpace = 'normal'
                messageSpan.textContent = noMergeTagSearchResultsMessage
                messageElement.appendChild(messageSpan)

                element.appendChild(messageElement)
            }
        }

        const selectItem = (index) => {
            if (!currentProps) return

            const items = currentProps.items || []
            const item = items[index]

            if (item) {
                currentProps.command({ id: item.id })
            }
        }

        const scrollToSelected = () => {
            if (!element || !currentProps || currentProps.items.length === 0)
                return

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

            const items = currentProps.items || []
            if (items.length === 0) return

            selectedIndex = (selectedIndex + items.length - 1) % items.length
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
            selectItem(selectedIndex)
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
            },
        }
    },
})
