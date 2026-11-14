export async function requestLazyLoadedState(
    component,
    configuration,
    targets,
    controller,
) {
    const identifier = Object.keys(targets)[0]
    const parameters = configuration.arguments.length
        ? [...configuration.arguments, [targets]]
        : [targets]
    let isStale = false
    let cancelRequest = () => {}

    const unsubscribe = component.$wire.$interceptMessage(
        ({ message, cancel, onSend, onSuccess, onError }) => {
            if (
                !Array.from(message.actions).some(
                    (action) =>
                        action.name === configuration.method &&
                        Object.hasOwn(
                            configuration.arguments.length
                                ? (action.params[2]?.[0] ?? {})
                                : (action.params[0] ?? {}),
                            identifier,
                        ),
                )
            ) {
                return
            }

            cancelRequest = cancel
            let snapshot

            onSend(() => {
                snapshot = component.snapshotEncoded
            })

            onSuccess(() => {
                // Cancel before Livewire synchronizes an obsolete parent snapshot,
                // including when a partial/renderless update kept the cell alive.
                if (component.snapshotEncoded !== snapshot) {
                    isStale = true
                    cancel()
                }
            })

            onError(({ response, preventDefault }) => {
                if (response.status !== 419) {
                    preventDefault()
                }
            })

            if (controller.signal.aborted) {
                cancel()
            }
        },
    )
    const abort = () => cancelRequest()
    controller.signal.addEventListener('abort', abort)

    try {
        const html = await Livewire.fireAction(
            component,
            configuration.method,
            parameters,
            { async: true },
        )

        return { html, isStale: false }
    } catch (error) {
        if (isStale) {
            return { isStale: true }
        }

        throw error
    } finally {
        unsubscribe()
        controller.signal.removeEventListener('abort', abort)
    }
}
