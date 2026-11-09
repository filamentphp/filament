import recordUpdates from '../../../../support/resources/js/utilities/record-updates.js'

export default (configuration) =>
    recordUpdates({
        ...configuration,
        authorizationMethod: 'authorizeRecordUpdates',
        refreshMethod: 'refreshRecordPage',
        onRefreshed: (title) => (document.title = title),
    })
