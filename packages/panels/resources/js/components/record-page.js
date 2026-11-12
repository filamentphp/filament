import eventListener from '../../../../support/resources/js/utilities/event-listener.js'

export default (configuration) => {
    const listener = eventListener({
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
    const pendingSaves = new Map()
    const completedSaves = new Set()
    let removeSaveInterceptor
    let isDestroyed = false

    return {
        ...listener,

        init() {
            listener.init.call(this)

            if (!configuration.refreshForm) return

            removeSaveInterceptor = this.$wire.$interceptMessage(
                'save',
                ({ message, onSuccess, onFinish }) => {
                    const operationId = Array.from(
                        crypto.getRandomValues(new Uint8Array(16)),
                        (byte) => byte.toString(16).padStart(2, '0'),
                    ).join('')
                    const save = { receivedEvent: false, reconciled: false }
                    pendingSaves.set(operationId, save)

                    message.calls
                        .filter((call) => call.method === 'save')
                        .forEach((call) => {
                            call.metadata.filamentRecordSave = operationId
                        })
                    // Livewire may already have serialized this request for
                    // its legacy request interceptors.
                    message.request.options.body = JSON.stringify(
                        message.request.payload,
                    )

                    onSuccess(({ payload, onMorphed }) => {
                        const acknowledgement =
                            payload.effects.filamentRecordSave

                        if (acknowledgement?.id !== operationId) return

                        onMorphed(() => {
                            if (isDestroyed || message.isCancelled()) return

                            save.reconciled = true
                            document.title = acknowledgement.title
                        })
                    })

                    onFinish(() => {
                        if (!pendingSaves.delete(operationId) || isDestroyed) {
                            return
                        }

                        if (save.reconciled && !message.isCancelled()) {
                            completedSaves.add(operationId)

                            // Evicting an old ID can only cause a redundant
                            // refresh; it cannot hide a subsequent change.
                            if (completedSaves.size > 100) {
                                completedSaves.delete(
                                    completedSaves.values().next().value,
                                )
                            }
                        } else if (save.receivedEvent) {
                            // A failed or cancelled response has not applied
                            // the saved state. Do not lose its invalidation.
                            queueMicrotask(() => {
                                if (!isDestroyed) this.receive({})
                            })
                        }
                    })
                },
            )
        },

        async receive(event) {
            if (event?.operationId) {
                if (completedSaves.has(event.operationId)) return

                const save = pendingSaves.get(event.operationId)

                if (save) {
                    save.receivedEvent = true
                    return
                }
            }

            return listener.receive.call(this, event)
        },

        destroy() {
            isDestroyed = true
            removeSaveInterceptor?.()
            removeSaveInterceptor = null
            pendingSaves.clear()
            completedSaves.clear()
            listener.destroy.call(this)
        },
    }
}
