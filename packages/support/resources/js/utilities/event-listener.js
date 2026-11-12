// Shared across the separately bundled actions and panels packages.
const listenersKey = Symbol.for('filament.eventListeners')

function getMessageListeners(message) {
    const actions = Array.from(message.actions)
    const listeners = message.component.el[listenersKey]?.listeners

    if (!actions.length || !listeners) return null

    const matches = actions.map((action) =>
        Array.from(listeners).find((listener) => listener.matches(action)),
    )

    return matches.every(Boolean) ? matches : null
}

function callListener(listener, method, parameters) {
    return new Promise((resolve, reject) => {
        const componentListeners = listener.componentListeners

        componentListeners.queue.push({
            listener,
            method,
            parameters,
            resolve,
            reject,
        })

        processListenerQueue(componentListeners)
    })
}

async function processListenerQueue(componentListeners) {
    if (componentListeners.isProcessing) return

    componentListeners.isProcessing = true

    try {
        while (componentListeners.queue.length) {
            const { listener, method, parameters, resolve, reject } =
                componentListeners.queue.shift()

            if (!listener.isActive()) {
                resolve(null)
                continue
            }

            try {
                resolve(await listener.call(method, parameters))
            } catch (error) {
                reject(error)
            }
        }
    } finally {
        componentListeners.isProcessing = false
    }
}

export default ({
    eventType = 'webSocket',
    channel,
    channelType = 'channel',
    eventName,
    echoConfiguration,
    authorizationMethod = null,
    refreshMethod,
    parameters = [],
    passEvent = true,
    refreshOnSubscription = false,
    coalesce = false,
    onRefreshed = () => {},
}) => {
    let echo
    let owner
    let isDestroyed = false
    let isRefreshing = false
    const pendingEvents = []
    let registerEchoListener
    let removeLivewireListener
    let removeRequestInterceptor
    let removeMessageInterceptor

    const listener = {
        matches: (action) =>
            [authorizationMethod, refreshMethod].includes(action.name) &&
            parameters.every(
                (parameter, index) => action.params[index] === parameter,
            ),
        isActive: () => !isDestroyed && owner?.$el.isConnected,
        call: (method, parameters) => owner.$wire[method](...parameters),
        componentListeners: null,
    }

    return {
        init() {
            owner = this
            listener.componentListeners = this.$wire.$el[listenersKey] ??= {
                listeners: new Set(),
                queue: [],
                isProcessing: false,
            }
            listener.componentListeners.listeners.add(listener)

            removeMessageInterceptor = this.$wire.$interceptMessage(
                ({ message, onSuccess, cancel }) => {
                    const listeners = getMessageListeners(message)

                    if (!listeners) return

                    onSuccess(() => {
                        // Cancel before Livewire merges the snapshot, not
                        // merely after the action's promise resolves.
                        if (
                            listeners.some((listener) => !listener.isActive())
                        ) {
                            cancel()
                        }
                    })
                },
            )

            removeRequestInterceptor = this.$wire.$interceptRequest(
                ({ request, onError }) => {
                    if (
                        request.messages.size &&
                        Array.from(request.messages).every((message) =>
                            getMessageListeners(message),
                        )
                    ) {
                        onError(({ preventDefault }) => preventDefault())
                    }
                },
            )

            if (eventType === 'livewire') {
                const receiveGlobalEvent = (event) =>
                    owner?.receive(event.detail || {})
                const receiveTargetedEvent = (event) => {
                    if (!event.__livewire || event.bubbles) return

                    owner?.receive(event.detail || {})
                }
                const componentElement = this.$wire.$el

                window.addEventListener(eventName, receiveGlobalEvent)
                componentElement.addEventListener(
                    eventName,
                    receiveTargetedEvent,
                )
                removeLivewireListener = () => {
                    window.removeEventListener(eventName, receiveGlobalEvent)
                    componentElement.removeEventListener(
                        eventName,
                        receiveTargetedEvent,
                    )
                }

                return
            }

            registerEchoListener = () => {
                if (echo || isDestroyed) {
                    return
                }

                echo = new window.EchoFactory({
                    ...echoConfiguration,
                    withoutInterceptors: true,
                    ...(authorizationMethod
                        ? {
                              channelAuthorization: {
                                  customHandler: async (
                                      { socketId },
                                      callback,
                                  ) => {
                                      let authorization

                                      try {
                                          authorization = await callListener(
                                              listener,
                                              authorizationMethod,
                                              [...parameters, socketId],
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
                          }
                        : {}),
                })

                const subscription = echo[channelType](channel)

                if (
                    channelType === 'join' &&
                    ['here', 'joining', 'leaving'].includes(eventName)
                ) {
                    subscription[eventName]((event) => owner?.receive(event))
                } else {
                    subscription.listen(eventName, (event) =>
                        owner?.receive(event),
                    )
                }

                if (refreshOnSubscription) {
                    subscription.subscribed(() => owner?.receive({}))
                }
            }

            window.addEventListener('EchoLoaded', registerEchoListener)

            if (window.EchoFactory) {
                registerEchoListener()
            }
        },

        async receive(event) {
            if (isDestroyed) return

            if (coalesce) pendingEvents.length = 0

            pendingEvents.push(event)

            // Generic events enter the component FIFO immediately. Only
            // invalidations coalesce while waiting for their current call.
            if (coalesce && isRefreshing) {
                return
            }

            isRefreshing = true

            try {
                while (pendingEvents.length && !isDestroyed) {
                    const event = pendingEvents.shift()

                    try {
                        const result = await callListener(
                            listener,
                            refreshMethod,
                            [...parameters, ...(passEvent ? [event] : [])],
                        )

                        if (
                            result === null ||
                            result === false ||
                            isDestroyed ||
                            !this.$el.isConnected
                        ) {
                            pendingEvents.length = 0
                            return
                        }

                        onRefreshed(result)
                    } catch {
                        // Keep the current modal on failure, but still process
                        // events received during the failed request.
                    }
                }
            } finally {
                isRefreshing = false
            }
        },

        destroy() {
            isDestroyed = true
            pendingEvents.length = 0

            window.removeEventListener('EchoLoaded', registerEchoListener)
            removeLivewireListener?.()
            removeRequestInterceptor?.()
            removeMessageInterceptor?.()
            if (listener.componentListeners) {
                listener.componentListeners.listeners.delete(listener)
                listener.componentListeners.queue =
                    listener.componentListeners.queue.filter((call) => {
                        if (call.listener !== listener) return true

                        call.resolve(null)
                        return false
                    })
            }

            // Disconnect first to settle pending subscriptions before leaving.
            echo?.disconnect()
            echo?.leave(channel)

            // Pusher retains disconnected clients and their authorizers.
            // None of their callbacks may retain the removed Alpine owner.
            owner = null
            listener.componentListeners = null
            removeLivewireListener = null
            removeRequestInterceptor = null
            removeMessageInterceptor = null
            registerEchoListener = null
            echo = null
        },
    }
}
