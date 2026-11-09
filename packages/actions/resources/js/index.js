import modals from './components/modals.js'
import recordUpdates from '../../../support/resources/js/utilities/record-updates.js'

document.addEventListener('alpine:init', () => {
    window.Alpine.data('filamentActionModals', modals)
    window.Alpine.data('filamentActionRecordUpdates', (configuration) =>
        recordUpdates({
            ...configuration,
            authorizationMethod: 'authorizeActionRecordUpdates',
            refreshMethod: 'refreshAction',
        }),
    )
})
