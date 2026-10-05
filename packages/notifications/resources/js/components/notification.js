import { once } from 'alpinejs/src/utils/once'

export default (Alpine) => {
    Alpine.data('notificationComponent', ({ notification }) => ({
        isShown: false,

        computedStyle: null,

        transitionDuration: null,

        transitionEasing: null,

        closeTimeout: null,

        durationTimeout: null,

        transitionEffect: null,

        unsubscribeLivewireHook: null,

        isDestroyed: false,

        init: function () {
            this.computedStyle = window.getComputedStyle(this.$el)

            this.transitionDuration =
                parseFloat(this.computedStyle.transitionDuration) * 1000

            this.transitionEasing = this.computedStyle.transitionTimingFunction

            this.configureTransitions()
            this.configureAnimations()

            if (
                notification.duration &&
                notification.duration !== 'persistent'
            ) {
                this.durationTimeout = setTimeout(() => {
                    if (!this.$el.matches(':hover')) {
                        this.close()

                        return
                    }

                    this.$el.addEventListener('mouseleave', () => this.close())
                }, notification.duration)
            }

            this.isShown = true
        },

        configureTransitions: function () {
            const display = this.computedStyle.display

            const show = () => {
                Alpine.mutateDom(() => {
                    this.$el.style.setProperty('display', display)
                    this.$el.style.setProperty('visibility', 'visible')
                })
                this.$el._x_isShown = true
            }

            const hide = () => {
                Alpine.mutateDom(() => {
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

            this.transitionEffect = Alpine.effect(() => {
                if (this.isDestroyed) {
                    return
                }

                toggle(this.isShown)
            })
        },

        configureAnimations: function () {
            let animation

            this.unsubscribeLivewireHook = Livewire.hook(
                'commit',
                ({ component, commit, succeed, fail, respond }) => {
                    if (
                        !component.snapshot.data
                            .isFilamentNotificationsComponent
                    ) {
                        return
                    }

                    // Calling `el.getBoundingClientRect()` from outside `requestAnimationFrame()` can
                    // occasionally cause the page to scroll to the top.
                    requestAnimationFrame(() => {
                        if (this.isDestroyed) {
                            return
                        }

                        const getTop = () =>
                            this.$el.getBoundingClientRect().top
                        const oldTop = getTop()

                        respond(() => {
                            if (this.isDestroyed) {
                                return
                            }

                            animation = () => {
                                if (this.isDestroyed || !this.isShown) {
                                    return
                                }

                                this.$el.animate(
                                    [
                                        {
                                            transform: `translateY(${
                                                oldTop - getTop()
                                            }px)`,
                                        },
                                        { transform: 'translateY(0px)' },
                                    ],
                                    {
                                        duration: this.transitionDuration,
                                        easing: this.transitionEasing,
                                    },
                                )
                            }

                            this.$el
                                .getAnimations()
                                .forEach((animation) => animation.finish())
                        })

                        succeed(({ snapshot, effect }) => {
                            if (this.isDestroyed) {
                                return
                            }

                            animation()
                        })
                    })
                },
            )
        },

        close: function () {
            clearTimeout(this.closeTimeout)
            clearTimeout(this.durationTimeout)

            this.isShown = false

            this.closeTimeout = setTimeout(
                () =>
                    window.dispatchEvent(
                        new CustomEvent('notificationClosed', {
                            detail: {
                                id: notification.id,
                            },
                        }),
                    ),
                this.transitionDuration,
            )
        },

        markAsRead: function () {
            window.dispatchEvent(
                new CustomEvent('markedNotificationAsRead', {
                    detail: {
                        id: notification.id,
                    },
                }),
            )
        },

        markAsUnread: function () {
            window.dispatchEvent(
                new CustomEvent('markedNotificationAsUnread', {
                    detail: {
                        id: notification.id,
                    },
                }),
            )
        },

        destroy: function () {
            this.isDestroyed = true
            clearTimeout(this.closeTimeout)
            clearTimeout(this.durationTimeout)
            this.unsubscribeLivewireHook?.()
            Alpine.release(this.transitionEffect)
        },
    }))
}
