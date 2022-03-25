import Sortable from 'sortablejs';

export default (el, directive, component) => {
    if (directive.modifiers.length > 0) {
        return;
    }
    el.livewire_sortable = Sortable.create(el, {
        draggable: '[wire\\:sortable\\.item]',
        handle: el.querySelector('[wire\\:sortable\\.handle]') ? '[wire\\:sortable\\.handle]' : null,
        sort: true,
        dataIdAttr: 'wire:sortable.item',
        group: {
            name: el.getAttribute('wire:sortable'),
            pull: false,
            put: false,
        },
        store: {
            set: function (sortable) {
                let items = sortable.toArray().map((value, index) => {
                    return {
                        order: index + 1,
                        value: value,
                    };
                });

                component.call(directive.method, items);
            },
        },
    });
};