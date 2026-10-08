import { once } from 'alpinejs/src/utils/once'

export default (Alpine) => {
    Alpine.data('notificationComponent', ({ notification }) => ({
        isShown: false,

        computedStyle: null,

        transitionDuration: null,

        transitionEasing: null,

        closeTimeout: null,

        durationTimeout: null,

        remainingDuration: null,

        durationStartedAt: null,

        isHovered: false,

        isFocusedWithin: false,

        isClosing: false,

        focusOrigin: null,

        unsubscribeLivewireHook: null,

        transitionEffect: null,

        isDestroyed: false,

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
                if (this.$root.classList.contains('fi-inline')) {
                    this.durationTimeout = setTimeout(() => {
                        if (!this.$el.matches(':hover')) {
                            this.close()

                            return
                        }

                        this.$el.addEventListener('mouseleave', () =>
                            this.close(),
                        )
                    }, notification.duration)
                } else {
                    this.remainingDuration = notification.duration
                    this.isHovered = this.$el.matches(':hover')
                    this.isFocusedWithin = this.$el.contains(
                        document.activeElement,
                    )

                    if (!this.isHovered && !this.isFocusedWithin) {
                        this.resumeDuration()
                    }
                }
            }

            this.isShown = true
        },

        pauseDuration(reason) {
            if (this.isClosing) {
                return
            }

            const wasPaused = this.isHovered || this.isFocusedWithin

            if (reason === 'hover') {
                this.isHovered = true
            } else {
                this.isFocusedWithin = true
            }

            if (wasPaused || !this.durationTimeout) {
                return
            }

            this.remainingDuration = Math.max(
                0,
                this.remainingDuration -
                    (performance.now() - this.durationStartedAt),
            )

            clearTimeout(this.durationTimeout)
            this.durationTimeout = null
            this.durationStartedAt = null
        },

        resumeDuration(reason = null) {
            if (reason === 'hover') {
                this.isHovered = false
            } else if (reason === 'focus') {
                this.isFocusedWithin = false
            }

            if (
                this.isClosing ||
                this.isHovered ||
                this.isFocusedWithin ||
                this.remainingDuration === null ||
                this.durationTimeout
            ) {
                return
            }

            this.durationStartedAt = performance.now()
            this.durationTimeout = setTimeout(() => {
                this.durationTimeout = null
                this.durationStartedAt = null
                this.remainingDuration = 0
                this.close()
            }, this.remainingDuration)
        },

        handleFocusIn(event) {
            if (!this.$el.contains(event.relatedTarget)) {
                this.focusOrigin =
                    event.relatedTarget instanceof HTMLElement
                        ? event.relatedTarget
                        : null
            }

            this.pauseDuration('focus')
        },

        handleFocusOut(event) {
            if (this.$el.contains(event.relatedTarget)) {
                return
            }

            this.resumeDuration('focus')
        },

        handleEscape(event) {
            if (!this.$el.contains(document.activeElement)) {
                return
            }

            event.preventDefault()
            event.stopImmediatePropagation()

            this.dismiss()
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

            this.transitionEffect = Alpine.effect(() => {
                if (this.isDestroyed) {
                    return
                }

                toggle(this.isShown)
            })
        },

        configureAnimations() {
            // Inline notifications, such as those in the database
            // notifications modal, are removed instantly, without animation.
            if (this.$el.classList.contains('fi-inline')) {
                return
            }

            this.unsubscribeLivewireHook = Livewire.hook(
                'commit',
                ({ component, succeed }) => {
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

                        succeed(() => {
                            if (this.isDestroyed) {
                                return
                            }

                            // `succeed` runs before Livewire morphs the DOM, which it
                            // defers using two nested `queueMicrotask()` calls, so the
                            // animation is deferred in the same way to run once the DOM
                            // has been morphed, before the browser paints, so the new
                            // position can be measured and the animation started without
                            // the notification flashing in its final position.
                            queueMicrotask(() =>
                                queueMicrotask(() => {
                                    if (this.isDestroyed || !this.isShown) {
                                        return
                                    }

                                    // Finish any running animations so they do not distort
                                    // the measurement of the new position.
                                    this.$el
                                        .getAnimations()
                                        .forEach((animation) =>
                                            animation.finish(),
                                        )

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
                                }),
                            )
                        })
                    })
                },
            )
        },

        close(isImmediate = false) {
            if (this.isClosing) {
                return
            }

            this.isClosing = true

            clearTimeout(this.closeTimeout)
            clearTimeout(this.durationTimeout)
            this.durationTimeout = null
            this.durationStartedAt = null

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

            const livewireRoot = this.$el.closest('[wire\\:id]')

            this.isShown = false

            this.closeTimeout = setTimeout(() => {
                // Do not deliver an old dismissal to a new host after navigation.
                if (livewireRoot && !livewireRoot.isConnected) {
                    return
                }

                dispatchClosedEvent()
            }, this.transitionDuration)
        },

        dismiss() {
            const element = this.focusOrigin

            this.focusOrigin = null

            if (
                this.$el.contains(document.activeElement) &&
                element instanceof HTMLElement &&
                element.isConnected &&
                !this.$el.contains(element)
            ) {
                element.focus({ preventScroll: true })
            }

            this.close()
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
            this.isDestroyed = true
            // Keep the pending `notificationClosed` event so a dismissal is not
            // lost if Livewire removes this notification before its transition ends.
            clearTimeout(this.durationTimeout)
            this.durationTimeout = null
            this.unsubscribeLivewireHook?.()
            this.unsubscribeLivewireHook = null
            Alpine.release(this.transitionEffect)
        },
    }))
}
