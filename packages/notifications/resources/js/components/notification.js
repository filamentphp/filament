import { mutateDom } from 'alpinejs/src/mutation'
import { once } from 'alpinejs/src/utils/once'

export default (Alpine) => {
    Alpine.data('notificationComponent', ({ $wire, notification }) => ({
        isShown: false,

        computedStyle: null,

        init: function () {
            this.computedStyle = window.getComputedStyle(this.$el)

            this.setUpSelf()

            this.configureAnimations()

            if (notification.duration !== null) {
                setTimeout(() => this.close(), notification.duration)
            }

            this.$nextTick(() => {
                this.isShown = true
            })
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
                            easing: this.getTransitionTiming(),
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

                if (this.$el._x_transitioning) {
                    return
                }

                animation()
            })
        },

        close: function () {
            this.isShown = false

            this.$nextTick(() => {
                setTimeout(
                    () => $wire.close(notification.id),
                    this.getTransitionDuration()
                )
            })
        },

        getTop: function () {
            return this.$el.getBoundingClientRect().top
        },

        getTransitionDuration: function () {
            return this.computedStyle.transitionDuration.length !== 0
                ? parseFloat(this.computedStyle.transitionDuration) * 1000
                : 0
        },

        getTransitionTiming: function () {
            return this.computedStyle.transitionTimingFunction.length !== 0
                ? this.computedStyle.transitionTimingFunction
                : 'ease'
        },

        setUpSelf: function () {
            this.$el._x_isShown = false

            if (!this.$el._x_doHide)
                this.$el._x_doHide = () => {
                    mutateDom(() => {
                        if (this.$el._x_isShown) {
                            this.$el.style.setProperty(
                                'visibility',
                                'hidden',
                                undefined
                            )
                        } else {
                            this.$el.style.setProperty(
                                'display',
                                'none',
                                undefined
                            )
                        }
                    })
                }

            if (!this.$el._x_doShow)
                this.$el._x_doShow = () => {
                    mutateDom(() => {
                        this.$el.style.setProperty('display', 'flex', undefined)
                    })
                }

            let hide = () => {
                this.$el._x_doHide()
                this.$el._x_isShown = false
            }

            let show = () => {
                this.$el._x_doShow()
                this.$el._x_isShown = true
            }

            let clickAwayCompatibleShow = () => setTimeout(show)

            let toggle = once(
                (value) => (value ? show() : hide()),
                (value) => {
                    if (
                        typeof this.$el._x_toggleAndCascadeWithTransitions ===
                        'function'
                    ) {
                        this.$el._x_toggleAndCascadeWithTransitions(
                            this.$el,
                            value,
                            show,
                            hide
                        )
                    } else {
                        value ? clickAwayCompatibleShow() : hide()
                    }
                }
            )

            let oldValue
            let firstTime = true

            Alpine.effect(() => {
                toggle(this.isShown)
                oldValue = this.isShown
                firstTime = false
            })
        },
    }))
}
