export default (Alpine) => {
    Alpine.data('notificationComponent', ({ notification }) => ({
        notification: notification,
        show: false,
        top: null,
        computedStyle: null,
        skipAnimation: false,
        init() {
            this.top = this.$el.getBoundingClientRect().top
            this.computedStyle = window.getComputedStyle(this.$el)
            this.$nextTick(() => (this.show = true))

            if (this.notification.duration !== null) {
                setTimeout(() => this.close(), this.notification.duration)
            }

            Livewire.on('notificationClosed', (notification) => {
                this.skipAnimation = true

                if (notification.id === this.notification.id) {
                    this.show = false
                    return
                }

                const element = document.getElementById(
                    `notification-${notification.id}`
                )

                this.animate(
                    element.getBoundingClientRect().height +
                        parseFloat(
                            window.getComputedStyle(element).marginBottom
                        )
                )
            })

            Livewire.hook('message.processed', (message) => {
                if (this.skipAnimation) {
                    return
                }

                this.skipAnimation = false

                this.animate(this.top - this.$el.getBoundingClientRect().top)
            })
        },
        animate(offset) {
            this.$el.animate(
                [
                    { transform: `translateY(${offset}px)` },
                    { transform: `translateY(0px)` },
                ],
                {
                    duration: this.transitionDuration,
                    easing: this.computedStyle.transitionTimingFunction,
                }
            )

            this.top -= offset
        },
        close() {
            this.$wire.emit('notificationClosed', this.notification)
        },
        get transitionDuration() {
            const duration = this.computedStyle.transitionDuration

            if (duration === '') {
                return null
            }

            return parseFloat(duration) * 1000
        },
    }))
}
