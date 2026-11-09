export default ({
    channel,
    echoConfiguration,
    authorizationMethod,
    refreshMethod,
    parameters = [],
    onRefreshed = () => {},
}) => {
    let echo
    let isDestroyed = false
    let isRefreshing = false
    let needsRefresh = false
    let registerEchoListener
    let removeRequestInterceptor
    let removeMessageInterceptor

    return {
        init() {
            removeMessageInterceptor = this.$wire.$interceptMessage(
                ({ message, onSuccess, cancel }) => {
                    const actions = Array.from(message.actions)

                    if (
                        !actions.length ||
                        !actions.every(
                            (action) =>
                                [authorizationMethod, refreshMethod].includes(
                                    action.name,
                                ) &&
                                parameters.every(
                                    (parameter, index) =>
                                        action.params[index] === parameter,
                                ),
                        )
                    ) {
                        return
                    }

                    onSuccess(() => {
                        // Cancel before Livewire merges the snapshot, not
                        // merely after the action's promise resolves.
                        if (isDestroyed || !this.$el.isConnected) cancel()
                    })
                },
            )

            removeRequestInterceptor = this.$wire.$interceptRequest(
                ({ request, onError }) => {
                    if (
                        Array.from(request.messages).every((message) => {
                            const actions = Array.from(message.actions)

                            return (
                                actions.length &&
                                actions.every((action) =>
                                    [
                                        'authorizeRecordUpdates',
                                        'refreshRecordPage',
                                        'authorizeActionRecordUpdates',
                                        'refreshAction',
                                    ].includes(action.name),
                                )
                            )
                        })
                    ) {
                        onError(({ preventDefault }) => preventDefault())
                    }
                },
            )

            registerEchoListener = () => {
                if (echo || isDestroyed) {
                    return
                }

                echo = new window.EchoFactory({
                    ...echoConfiguration,
                    withoutInterceptors: true,
                    channelAuthorization: {
                        customHandler: async ({ socketId }, callback) => {
                            let authorization

                            try {
                                authorization = await this.$wire[
                                    authorizationMethod
                                ](...parameters, socketId)
                            } catch (error) {
                                if (!isDestroyed) callback(error)

                                return
                            }

                            if (isDestroyed) return

                            callback(
                                authorization
                                    ? null
                                    : new Error(
                                          'The record is no longer available.',
                                      ),
                                authorization,
                            )
                        },
                    },
                })

                echo.private(channel)
                    .listen('.filament.model-changed', () => this.refresh())
                    .subscribed(() => this.refresh())
            }

            window.addEventListener('EchoLoaded', registerEchoListener)

            if (window.EchoFactory) {
                registerEchoListener()
            }
        },

        async refresh() {
            needsRefresh = true

            if (isRefreshing || isDestroyed) {
                return
            }

            isRefreshing = true

            try {
                while (needsRefresh && !isDestroyed) {
                    needsRefresh = false

                    try {
                        const result = await this.$wire[refreshMethod](
                            ...parameters,
                        )

                        if (
                            result === null ||
                            result === false ||
                            isDestroyed ||
                            !this.$el.isConnected
                        ) {
                            return
                        }

                        onRefreshed(result)
                    } catch {
                        // Keep the current page on failure, but still process
                        // any notification received during the failed request.
                    }
                }
            } finally {
                isRefreshing = false
            }
        },

        destroy() {
            isDestroyed = true

            window.removeEventListener('EchoLoaded', registerEchoListener)
            removeRequestInterceptor?.()
            removeMessageInterceptor?.()
            echo?.disconnect()
        },
    }
}
