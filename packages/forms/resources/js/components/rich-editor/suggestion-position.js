import { computePosition, flip, shift } from '@floating-ui/dom'

export default function updateSuggestionPosition(editor, element) {
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
