export default (Alpine) => {
    Alpine.data('notificationComponent', ({
        notification,
    }) => ({

        init: function () {
            if (notification.duration !== null) {
                setTimeout(() => this.close(), notification.duration)
            }
        },

        close: function () {
            this.$wire.close(notification.id)
        },

    }))
}
