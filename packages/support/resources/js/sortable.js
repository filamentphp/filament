import Sortable from 'sortablejs'

window.Sortable = Sortable

export default (Alpine) => {
    Alpine.directive('sortable', (el) => {
        let animation = parseInt(el.dataset?.sortableAnimationDuration)

        if (animation !== 0 && !animation) {
            animation = 300
        }

        el.sortable = Sortable.create(el, {
            draggable: '[x-sortable-item]',
            handle: '[x-sortable-handle]',
            dataIdAttr: 'x-sortable-item',
            animation: animation,
            ghostClass: 'fi-sortable-ghost',
        })
    })
}
