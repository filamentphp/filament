import { mutateDom } from 'alpinejs/src/mutation'
import { once } from 'alpinejs/src/utils/once'

export default (Alpine) => {
    Alpine.data('notificationComponent', ({ notification }) => ({
        isShown: false,

        computedStyle: null,

        init: function () {
            this.computedStyle = window.getComputedStyle(this.$el)

            this.configureTransitions()
            this.configureAnimations()

            if (
                notification.duration &&
                notification.duration !== 'persistent'
            ) {
                setTimeout(() => this.close(), notification.duration)
            }

            this.isShown = true
        },

        configureTransitions: function () {
            const display = this.computedStyle.display

            const show = () => {
                mutateDom(() => {
                    this.$el.style.setProperty('display', display)
                    this.$el.style.setProperty('visibility', 'visible')
                })
                this.$el._x_isShown = true
            }

            const hide = () => {
                mutateDom(() => {
                    this.$el._x_isShown
                        ? this.$el.style.setProperty('visibility', 'hidden')
                        : this.$el.style.setProperty('display', 'none')
                })
            }

            const toggle = once(
                (value) => (value ? show() : hide()),
                (value) => {
                    this.$el._x_toggleAndCascadeWithTransitions(
                        this.$el,
                        value,
                        show,
                        hide,
                    )
                },
            )

            Alpine.effect(() => toggle(this.isShown))
        },

        configureAnimations: function () {
            let animation

            // @todo: This hook was in Livewire 2 but I can't find it in Livewire v3 yet.
            Livewire.hook('message.received', (_, component) => {
                if (
                    !component.snapshot.isFilamentNotificationsComponent
                ) {
                    return
                }

                const getTop = () => this.$el.getBoundingClientRect().top
                const oldTop = getTop()

                animation = () => {
                    this.$el.animate(
                        [
                            { transform: `translateY(${oldTop - getTop()}px)` },
                            { transform: 'translateY(0px)' },
                        ],
                        {
                            duration: this.getTransitionDuration(),
                            easing: this.computedStyle.transitionTimingFunction,
                        },
                    )
                }

                this.$el
                    .getAnimations()
                    .forEach((animation) => animation.finish())
            })

            // @todo: This hook was in Livewire 2 but I can't find it in Livewire v3 yet.
            Livewire.hook('message.processed', (_, component) => {
                if (
                    !component.snapshot.isFilamentNotificationsComponent
                ) {
                    return
                }

                if (!this.isShown) {
                    return
                }

                animation()
            })

            on('request', (component) => {
                return () => {
                    // This would be equivalent to "message.received" I think

                    // Then you could do like: queueMicrotask(() => { ...  }) for message.processed
                }
            })
        },

        close: function () {
            this.isShown = false

            setTimeout(
                () => window.dispatchEvent(new CustomEvent('notificationClosed', { detail: {
                    id: notification.id,
                }})),
                this.getTransitionDuration(),
            )
        },

        markAsRead: function () {
            window.dispatchEvent(new CustomEvent('markedNotificationAsRead', { detail: {
                id: notification.id,
            }}))
        },

        markAsUnread: function () {
            window.dispatchEvent(new CustomEvent('markedNotificationAsUnread', { detail: {
                id: notification.id,
            }}))
        },

        getTransitionDuration: function () {
            return parseFloat(this.computedStyle.transitionDuration) * 1000
        },
    }))
}
