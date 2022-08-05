export default (Alpine) => {
    Alpine.data('notificationComponent', ({ $wire, notification }) => ({
        phase: 'enter-start',

        computedStyle: null,

        hasTransitionLeaveAttribute: false,

        init: function () {
            this.computedStyle = window.getComputedStyle(this.$el)

            this.hasTransitionLeaveAttribute =
                this.$el.hasAttribute('x-transition:leave') ||
                this.$el.hasAttribute('x-transition:leave-start') ||
                this.$el.hasAttribute('x-transition:leave-end')

            this.configureAnimations()

            if (notification.duration !== null) {
                setTimeout(() => this.close(), notification.duration)
            }

            this.$nextTick(() => (this.phase = 'enter-end'))
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

                if (this.phase.startsWith('leave-')) {
                    return
                }

                animation()
            })
        },

        close: function () {
            this.phase = 'leave-start'

            this.$nextTick(() => {
                setTimeout(
                    () => $wire.close(notification.id),
                    this.hasTransitionLeaveAttribute
                        ? this.getTransitionDuration()
                        : 0
                )

                this.phase = 'leave-end'
            })
        },

        getTop: function () {
            return this.$el.getBoundingClientRect().top
        },

        getTransitionDuration: function () {
            return parseFloat(this.computedStyle.transitionDuration) * 1000
        },
    }))
}
