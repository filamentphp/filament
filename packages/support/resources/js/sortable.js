import Sortable from 'sortablejs'

window.Sortable = Sortable

export default (Alpine) => {
    Alpine.directive('sortable', (el) => {
        let animation = parseInt(el.dataset?.sortableAnimationDuration)

        if (animation !== 0 && !animation) {
            animation = 300
        }

        el.sortable = Sortable.create(el, {
            group: el.getAttribute('x-sortable-group'),
            draggable: '[x-sortable-item]',
            handle: '[x-sortable-handle]',
            dataIdAttr: 'x-sortable-item',
            animation: animation,
            ghostClass: 'fi-sortable-ghost',
            onEnd(event) {
                // https://github.com/filamentphp/filament/issues/17402
                const {
                    item: draggedNode,
                    to: parentNode,
                    oldDraggableIndex,
                    newDraggableIndex,
                } = event

                if (oldDraggableIndex === newDraggableIndex) {
                    return
                }

                const draggableSelector = this.options.draggable
                const previousNode = parentNode.querySelectorAll(
                    `:scope > ${draggableSelector}`,
                )[newDraggableIndex - 1]

                if (previousNode) {
                    parentNode.insertBefore(
                        draggedNode,
                        previousNode.nextSibling,
                    )
                }
            },
        })
    })
}
