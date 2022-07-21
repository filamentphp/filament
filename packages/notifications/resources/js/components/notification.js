export default (Alpine) => {
    Alpine.data('notificationComponent', ({ notification }) => ({
        isVisible: false,

        top: null,

        init: function () {
            this.$nextTick(() => (this.isVisible = true))

            if (notification.duration !== null) {
                setTimeout(() => this.close(), notification.duration)
            }

            Livewire.hook('message.received', () => {
                this.top = this.$refs.notification.getBoundingClientRect().top
            })

            Livewire.hook('message.processed', () => {
                this.animate()
            })
        },

        animate: function () {
            const top = this.$refs.notification.getBoundingClientRect().top

            this.$refs.notification.animate(
                [
                    { transform: `translateY(${this.top - top}px)` },
                    { transform: 'translateY(0px)' },
                ],
                {
                    duration:
                        parseFloat(
                            window.getComputedStyle(this.$refs.notification)
                                .transitionDuration
                        ) * 1000,
                    easing: window.getComputedStyle(this.$refs.notification)
                        .transitionTimingFunction,
                }
            )

            this.top = top
        },

        close: function () {
            this.$wire.close(notification.id)
        },
    }))
}
