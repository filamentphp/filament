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
        return mergeTags.filter((item) =>
            item.toLowerCase().replace(/\s/g, '').includes(query.toLowerCase()),
        )
    },

    render: () => {
        let element
        let selectedIndex = 0
        let currentProps = null

        const createDropdown = () => {
            const dropdown = document.createElement('div')
            dropdown.className = 'fi-dropdown-panel fi-dropdown-list'

            return dropdown
        }

        const renderItems = () => {
            if (!element || !currentProps) return

            const items = currentProps.items || []

            // Clear existing items
            element.innerHTML = ''

            if (items.length) {
                items.forEach((item, index) => {
                    const button = document.createElement('button')
                    button.className = `fi-dropdown-list-item fi-dropdown-list-item-label ${index === selectedIndex ? 'fi-selected' : ''}`
                    button.textContent = item
                    button.type = 'button'
                    button.addEventListener('click', () => selectItem(index))
                    element.appendChild(button)
                })
            } else {
                const noSearchResultsMessage = document.createElement('div')
                noSearchResultsMessage.className = 'fi-dropdown-header'
                noSearchResultsMessage.textContent =
                    noMergeTagSearchResultsMessage
                element.appendChild(noSearchResultsMessage)
            }
        }

        const selectItem = (index) => {
            if (!currentProps) return

            const items = currentProps.items || []
            const item = items[index]

            if (item) {
                currentProps.command({ id: item })
            }
        }

        const upHandler = () => {
            if (!currentProps) return

            const items = currentProps.items || []
            if (items.length === 0) return

            selectedIndex = (selectedIndex + items.length - 1) % items.length
            renderItems()
        }

        const downHandler = () => {
            if (!currentProps) return

            const items = currentProps.items || []
            if (items.length === 0) return

            selectedIndex = (selectedIndex + 1) % items.length
            renderItems()
        }

        const enterHandler = () => {
            selectItem(selectedIndex)
        }

        return {
            onStart: (props) => {
                // Store current props
                currentProps = props

                // Reset selected index when items change
                selectedIndex = 0

                // Create dropdown element
                element = createDropdown()
                element.style.position = 'absolute'

                // Render initial items
                renderItems()

                // Append to DOM
                document.body.appendChild(element)

                if (!props.clientRect) {
                    return
                }

                updatePosition(props.editor, element)
            },

            onUpdate: (props) => {
                // Store current props
                currentProps = props

                // Reset selected index when items change
                selectedIndex = 0

                // Update dropdown items
                renderItems()

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
