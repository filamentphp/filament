import { once } from 'alpinejs/src/utils/once'

export default (Alpine) => {
    Alpine.data('notificationComponent', ({ notification }) => ({
        isShown: false,

        computedStyle: null,

        transitionDuration: null,

        transitionEasing: null,

        unsubscribeLivewireHook: null,

        init() {
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
                setTimeout(() => {
                    if (!this.$el.matches(':hover')) {
                        this.close()

                        return
                    }

                    this.$el.addEventListener('mouseleave', () => this.close())
                }, notification.duration)
            }

            this.isShown = true
        },

        configureTransitions() {
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

            Alpine.effect(() => toggle(this.isShown))
        },

        configureAnimations() {
            // Inline notifications, such as those in the database
            // notifications modal, are removed instantly, without animation.
            if (this.$el.classList.contains('fi-inline')) {
                return
            }

            this.unsubscribeLivewireHook = Livewire.interceptMessage(
                ({ message, onSuccess }) => {
                    if (
                        !message.component.snapshot.data
                            .isFilamentNotificationsComponent
                    ) {
                        return
                    }

                    // Calling `el.getBoundingClientRect()` from outside `requestAnimationFrame()` can
                    // occasionally cause the page to scroll to the top.
                    requestAnimationFrame(() => {
                        const getTop = () =>
                            this.$el.getBoundingClientRect().top
                        const oldTop = getTop()

                        onSuccess(({ onRender }) => {
                            // `onRender` runs once the DOM has been morphed, inside a
                            // `requestAnimationFrame()` before the browser paints, so the
                            // new position can be measured and the animation started
                            // without the notification flashing in its final position.
                            onRender(() => {
                                if (!this.isShown) {
                                    return
                                }

                                // Finish any running animations so they do not distort
                                // the measurement of the new position.
                                this.$el
                                    .getAnimations()
                                    .forEach((animation) => animation.finish())

                                const newTop = getTop()

                                if (oldTop === newTop) {
                                    return
                                }

                                // Honor `prefers-reduced-motion`: `element.animate()`
                                // (the Web Animations API) is not covered by the CSS
                                // reduced-motion reset, so skip the FLIP reposition
                                // entirely — the element is already at its final
                                // position after the morph.
                                if (
                                    window.matchMedia(
                                        '(prefers-reduced-motion: reduce)',
                                    ).matches
                                ) {
                                    return
                                }

                                this.$el.animate(
                                    [
                                        {
                                            transform: `translateY(${oldTop - newTop}px)`,
                                        },
                                        { transform: 'translateY(0px)' },
                                    ],
                                    {
                                        duration: this.transitionDuration,
                                        easing: this.transitionEasing,
                                    },
                                )
                            })
                        })
                    })
                },
            )
        },

        close(isImmediate = false) {
            const dispatchClosedEvent = () =>
                window.dispatchEvent(
                    new CustomEvent('notificationClosed', {
                        detail: {
                            id: notification.id,
                        },
                    }),
                )

            if (isImmediate === true) {
                this.isShown = false

                dispatchClosedEvent()

                return
            }

            // Inline notifications, such as those in the database
            // notifications modal, are part of a list, so they are removed
            // from it as soon as possible instead of fading out first.
            if (this.$root.classList.contains('fi-inline')) {
                dispatchClosedEvent()

                return
            }

            this.isShown = false

            setTimeout(dispatchClosedEvent, this.transitionDuration)
        },

        markAsRead() {
            window.dispatchEvent(
                new CustomEvent('markedNotificationAsRead', {
                    detail: {
                        id: notification.id,
                    },
                }),
            )
        },

        markAsUnread() {
            window.dispatchEvent(
                new CustomEvent('markedNotificationAsUnread', {
                    detail: {
                        id: notification.id,
                    },
                }),
            )
        },

        destroy() {
            this.unsubscribeLivewireHook?.()
        },
    }))
}
