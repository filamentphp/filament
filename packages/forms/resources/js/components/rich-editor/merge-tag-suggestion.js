import updatePosition from './suggestion-position.js'
import normalizeSearchString from '../../../../../support/resources/js/utilities/normalize-search-string.js'

export default ({ mergeTags, noMergeTagSearchResultsMessage }) => ({
    items: ({ query }) => {
        const search = normalizeSearchString(query)

        return Object.entries(mergeTags)
            .filter(
                ([id, label]) =>
                    normalizeSearchString(id)
                        .replace(/\s/g, '')
                        .includes(search) ||
                    normalizeSearchString(label)
                        .replace(/\s/g, '')
                        .includes(search),
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
                element.style.zIndex = '50'

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
