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
            onEnd: function (evt) {
                const order = this.toArray()
                const draggedId = evt.item.getAttribute('x-sortable-item')
                el.dispatchEvent(
                    new CustomEvent('fi-sortable-end', {
                        detail: { order, draggedId },
                        bubbles: true,
                    }),
                )
            },
        })
    })
}
