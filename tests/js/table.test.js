import { test } from 'node:test'
import assert from 'node:assert/strict'
import table from '../../packages/tables/resources/js/components/table.js'

test('table selection entanglement and actions use the Alpine Livewire context', () => {
    const calls = []
    const selection = ['record-7']
    const component = table.call(
        {
            $wire: {
                $entangle: (path) => {
                    calls.push(['entangle', path])
                    return selection
                },
                set: (...parameters) => calls.push(['set', ...parameters]),
                mountAction: (...parameters) =>
                    calls.push(['action', ...parameters]),
            },
        },
        { currentSelectionLivewireProperty: 'data.selected' },
    )

    assert.equal(component.entangledSelectedRecords, selection)
    component.selectedRecords = new Set(['record-9'])
    component.deselectedRecords = new Set(['record-2'])
    component.mountAction('export', { format: 'csv' })

    assert.deepEqual(calls, [
        ['entangle', 'data.selected'],
        ['set', 'isTrackingDeselectedTableRecords', false, false],
        ['set', 'selectedTableRecords', ['record-9'], false],
        ['set', 'deselectedTableRecords', ['record-2'], false],
        ['action', 'export', { format: 'csv' }],
    ])
})
