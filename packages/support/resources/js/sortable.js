import Sortable from 'sortablejs'

window.Sortable = Sortable

export default (Alpine) => {
    Alpine.directive('sortable', (el) => {
        el.sortable = Sortable.create(el, {
            draggable: '[x-sortable-item]',
            handle: '[x-sortable-handle]',
            dataIdAttr: 'x-sortable-item',
        })
    })
}
