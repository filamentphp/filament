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
            onEnd(evt) {
                const dragged = evt.item
                const parent = dragged.parentNode
                const children = Array.from(parent.childNodes)

                const draggedIndex = children.indexOf(dragged)

                if (draggedIndex === children.length - 1) {
                    const commentNode = children
                        .slice(0, draggedIndex)
                        .reverse()
                        .find(
                            (node) =>
                                node.nodeType === Node.COMMENT_NODE &&
                                (node.nodeValue
                                    .trim()
                                    .startsWith('[if ENDBLOCK]') ||
                                    node.nodeValue
                                        .trim()
                                        .startsWith('[if BLOCK]')),
                        )

                    if (commentNode) {
                        parent.insertBefore(dragged, commentNode)
                    }
                }
            },
        })
    })
}
