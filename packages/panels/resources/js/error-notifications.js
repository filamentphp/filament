document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ payload, fail }) => {
        fail(({ status, preventDefault }) => {
            const errorNotifications = window.filamentErrorNotifications

            if (!errorNotifications) {
                return
            }

            if (JSON.parse(payload).components.length === 1) {
                for (const component of JSON.parse(payload).components) {
                    if (
                        JSON.parse(component.snapshot).data
                            .isFilamentNotificationsComponent
                    ) {
                        return
                    }
                }
            }

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
    })
})
