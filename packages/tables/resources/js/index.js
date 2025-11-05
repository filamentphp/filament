import table from './components/table.js'
import tree from './components/tree.js'
import columnManager from './components/column-manager.js'

document.addEventListener('alpine:init', () => {
    window.Alpine.data('filamentTable', table)
    window.Alpine.data('filamentTableTree', tree)
    window.Alpine.data('filamentTableColumnManager', columnManager)
})
