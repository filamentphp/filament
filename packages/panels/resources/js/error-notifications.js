document.addEventListener('livewire:init', () => {
    Livewire.interceptRequest(({ request, onError, onFailure }) => {
        onError(({ response, preventDefault }) => {
            const errorNotifications = window.filamentErrorNotifications

            if (!errorNotifications) {
                return
            }

            try {
                const payload = request?.payload
                if (payload && JSON.parse(payload).components.length === 1) {
                    for (const component of JSON.parse(payload).components) {
                        if (
                            JSON.parse(component.snapshot).data
                                .isFilamentNotificationsComponent
                        ) {
                            return
                        }
                    }
                }
            } catch (error) {
                //
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

            const errorNotification = errorNotifications['']
            new FilamentNotification()
                .title(errorNotification.title)
                .body(errorNotification.body)
                .danger()
                .send()
        })
    })
})
