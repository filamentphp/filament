export default (Alpine) => {
    Alpine.data('notificationComponent', ({ $wire, notification }) => ({
        isEntering: true,

        isClosing: false,

        computedStyle: null,

        init: function () {
            this.computedStyle = window.getComputedStyle(this.$el)
            this.configureAnimations()

            if (notification.duration !== null) {
                setTimeout(() => this.close(), notification.duration)
            }

            this.$nextTick(() => (this.isEntering = false))
        },

        configureAnimations: function () {
            let animation

            Livewire.hook('message.received', (_, component) => {
                if (component.fingerprint.name !== 'notifications') {
                    return
                }

                const oldTop = this.getTop()

                animation = () => {
                    const newTop = this.getTop()

                    this.$el.animate(
                        [
                            { transform: `translateY(${oldTop - newTop}px)` },
                            { transform: 'translateY(0px)' },
                        ],
                        {
                            duration: this.getTransitionDuration(),
                            easing: this.computedStyle.transitionTimingFunction,
                        }
                    )
                }

                this.$el
                    .getAnimations()
                    .forEach((animation) => animation.finish())
            })

            Livewire.hook('message.processed', (_, component) => {
                if (component.fingerprint.name !== 'notifications') {
                    return
                }

                if (this.isClosing) {
                    return
                }

                animation()
            })
        },

        close: function () {
            this.isClosing = true

            setTimeout(
                () => $wire.close(notification.id),
                this.getTransitionDuration()
            )
        },

        getTop: function () {
            return this.$el.getBoundingClientRect().top
        },

        getTransitionDuration: function () {
            return parseFloat(this.computedStyle.transitionDuration) * 1000
        },
    }))
}
