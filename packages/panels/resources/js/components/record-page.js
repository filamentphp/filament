export default ({ channel, echoConfiguration }) => {
    let echo
    let isDestroyed = false
    let isRefreshing = false
    let needsRefresh = false
    let registerEchoListener
    let removeRequestInterceptor

    return {
        init() {
            removeRequestInterceptor = this.$wire.$interceptRequest(
                ({ request, onError }) => {
                    if (
                        Array.from(request.messages).every((message) =>
                            Array.from(message.actions).every((action) =>
                                [
                                    'authorizeRecordUpdates',
                                    'refreshRecordPage',
                                ].includes(action.name),
                            ),
                        )
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
                                authorization =
                                    await this.$wire.authorizeRecordUpdates(
                                        socketId,
                                    )
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
                        const title = await this.$wire.refreshRecordPage()

                        if (
                            title === null ||
                            isDestroyed ||
                            !this.$el.isConnected
                        ) {
                            return
                        }

                        document.title = title
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
            echo?.disconnect()
        },
    }
}
