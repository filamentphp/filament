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

export default ({ items = [] }) => ({
    // Support: items can be an array or a (async) function returning an array
    items: async ({ query }) => {
        // When items is a function, call it (it may return a Promise or array)
        if (typeof items === 'function') {
            try {
                const result = items({ query })
                return Array.isArray(result) ? result : await result
            } catch (e) {
                return []
            }
        }

        // items is a static array - apply local filtering when query is provided
        if (!query) return items

        const q = String(query).toLowerCase()
        return items.filter((item) => {
            const label = typeof item === 'string' ? item : (item?.label ?? item?.name ?? '')
            return String(label).toLowerCase().includes(q)
        })
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

        const handleSearch = () => {
            // no-op: Suggestion already passes filtered items via props.items
        }
        const renderItems = () => {
            if (!element || !currentProps) return

            const list = Array.isArray(currentProps.items) ? currentProps.items : []
            element.innerHTML = ''

            if (list.length) {
                list.forEach((raw, index) => {
                    const label = typeof raw === 'string' ? raw : (raw?.label ?? raw?.name ?? String(raw?.id ?? ''))
                    const id = typeof raw === 'object' ? (raw?.id ?? label) : label

                    const button = document.createElement('button')
                    button.className = `fi-dropdown-list-item fi-dropdown-list-item-label ${index === selectedIndex ? 'fi-selected' : ''}`
                    button.textContent = label
                    button.type = 'button'
                    button.addEventListener('click', () => selectItem(id, label))
                    element.appendChild(button)
                })
            }
        }

        const selectItem = (id, label) => {
            if (!currentProps) return
            currentProps.command({ id, label })
        }

        const upHandler = () => {
            if (!currentProps) return
            const list = Array.isArray(currentProps.items) ? currentProps.items : []
            if (!list.length) return
            selectedIndex = (selectedIndex + list.length - 1) % list.length
            renderItems()
        }

        const downHandler = () => {
            if (!currentProps) return
            const list = currentProps.items || []
            if (!list.length) return
            selectedIndex = (selectedIndex + 1) % list.length
            renderItems()
        }

        const enterHandler = () => {
            const list = currentProps?.items || []
            if (!list.length) return
            const raw = list[selectedIndex]
            const label = typeof raw === 'string' ? raw : (raw?.label ?? raw?.name ?? String(raw?.id ?? ''))
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

                if (!props.clientRect) return
                updatePosition(props.editor, element)
            },

            onUpdate: (props) => {
                currentProps = props
                selectedIndex = 0
                handleSearch()
                renderItems()
                if (!props.clientRect) return
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
