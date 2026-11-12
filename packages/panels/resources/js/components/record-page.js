import eventListener from '../../../../support/resources/js/utilities/event-listener.js'

export default (configuration) =>
    eventListener({
        ...configuration,
        channelType: 'private',
        eventName: '.filament.record-changed',
        authorizationMethod: 'authorizeRecordUpdates',
        refreshMethod: 'refreshRecordPage',
        passEvent: false,
        refreshOnSubscription: true,
        coalesce: true,
        refreshFormParameter: configuration.refreshForm ? 0 : null,
        onRefreshed: (title) => (document.title = title),
    })
