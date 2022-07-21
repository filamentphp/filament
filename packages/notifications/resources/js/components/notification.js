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
                this.top = this.$el.getBoundingClientRect().top
            })

            Livewire.hook('message.processed', () => {
                this.animate()
            })
        },

        animate: function () {
            const top = this.$el.getBoundingClientRect().top

            this.$el.animate(
                [
                    { transform: `translateY(${this.top - top}px)` },
                    { transform: 'translateY(0px)' },
                ],
                {
                    duration:
                        parseFloat(
                            window.getComputedStyle(this.$el).transitionDuration
                        ) * 1000,
                    easing: window.getComputedStyle(this.$el)
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
