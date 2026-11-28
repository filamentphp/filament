// The error notification is sent by a request to the notifications component. If that request also fails, a
// notification about it would fail the same way and repeat forever.
const isNotificationsComponentRequest = (request) => {
    try {
        const payload = request?.payload

        if (!payload || payload.components.length !== 1) {
            return false
        }

        return payload.components.every(
            (component) =>
                JSON.parse(component.snapshot).data
                    .isFilamentNotificationsComponent,
        )
    } catch (error) {
        return false
    }
}

document.addEventListener('livewire:init', () => {
    Livewire.interceptRequest(({ request, onError, onFailure }) => {
        onError(({ response, preventDefault }) => {
            const errorNotifications = window.filamentErrorNotifications

            if (!errorNotifications) {
                return
            }

            if (isNotificationsComponentRequest(request)) {
                return
            }

            const status = response?.status ?? ''
            const errorNotification =
                errorNotifications[status] ?? errorNotifications['']

            if (errorNotification.isDisabled === true) {
                return
            }

            preventDefault()

            if (errorNotification.isHidden === true) {
                return
            }

            new FilamentNotification()
                .title(errorNotification.title)
                .body(errorNotification.body)
                .danger()
                .send()
        })

        onFailure(() => {
            const errorNotifications = window.filamentErrorNotifications

            if (!errorNotifications) {
                return
            }

            if (isNotificationsComponentRequest(request)) {
                return
            }

            const errorNotification = errorNotifications['']
            new FilamentNotification()
                .title(errorNotification.title)
                .body(errorNotification.body)
                .danger()
                .send()
        })
    })
})
