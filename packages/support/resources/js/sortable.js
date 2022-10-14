import Sortable from 'sortablejs'

window.Sortable = Sortable

window.Livewire.directive('sortable', (el) => {
    el.sortable = Sortable.create(el, {
        draggable: '[wire\\:sortable\\.item]',
        handle: '[wire\\:sortable\\.handle]',
        dataIdAttr: 'wire:sortable.item',
    })
})

export default (Alpine) => {
    Alpine.directive('sortable', (el) => {
        el.sortable = Sortable.create(el, {
            draggable: '[x-sortable-item]',
            handle: '[x-sortable-handle]',
            dataIdAttr: 'x-sortable-item',
        })
    })
}
