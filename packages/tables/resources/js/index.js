import table, { tableFilters } from './components/table.js'
import columnManager from './components/column-manager.js'
import { grouping, sort } from './components/sort-and-grouping.js'

document.addEventListener('alpine:init', () => {
    window.Alpine.data('filamentTable', table)
    window.Alpine.data('filamentTableFilters', tableFilters)
    window.Alpine.data('filamentTableColumnManager', columnManager)
    window.Alpine.data('filamentTableSort', sort)
    window.Alpine.data('filamentTableGrouping', grouping)
})
