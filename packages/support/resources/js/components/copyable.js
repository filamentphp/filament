import tippy from 'tippy.js'

const interactiveSelector =
    'a[href], button, input, select, textarea, summary, [role="button"], [role="link"], [role="checkbox"], [role="switch"], [role="menuitem"], [tabindex]:not([tabindex="-1"]), [contenteditable]:not([contenteditable="false"])'

export default () => ({
    isStandalone: false,
    isCopying: false,
    isDestroyed: false,
    isSpacePressed: false,
    liveRegion: null,
    feedback: null,
    feedbackTimeout: null,
    feedbackSequence: 0,
    observer: null,

    bindings: {
        ['x-bind:role']() {
            return this.isStandalone ? 'button' : null
        },
        ['x-bind:tabindex']() {
            return this.isStandalone ? 0 : null
        },
        ['x-on:keydown.enter'](event) {
            if (!this.isStandalone || event.target !== this.$el) {
                return
            }

            event.preventDefault()

            if (!event.repeat) {
                this.$el.click()
            }
        },
        ['x-on:keydown.space'](event) {
            if (!this.isStandalone || event.target !== this.$el) {
                return
            }

            event.preventDefault()
            this.isSpacePressed = true
        },
        ['x-on:keyup.space'](event) {
            if (!this.isStandalone || event.target !== this.$el) {
                return
            }

            event.preventDefault()

            if (this.isSpacePressed) {
                this.isSpacePressed = false
                this.$el.click()
            }
        },
        ['x-on:blur']() {
            this.isSpacePressed = false
        },
    },

    init() {
        this.updateStandalone()

        this.observer = new MutationObserver((mutations) => {
            if (
                mutations.some(
                    (mutation) =>
                        mutation.target === this.$el &&
                        mutation.attributeName?.startsWith('x-on:click'),
                )
            ) {
                this.clearFeedback()
            }

            this.updateStandalone()
        })
        this.observer.observe(
            this.$el.closest('.fi-in-entry, .fi-ta-col') ??
                this.$el.parentElement,
            {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: [
                    'href',
                    'role',
                    'tabindex',
                    'contenteditable',
                    'x-on:click',
                    'x-on:click.prevent.stop',
                ],
            },
        )
    },

    updateStandalone() {
        if (this.isDestroyed || !this.$el.isConnected) {
            return
        }

        this.isStandalone =
            !this.$el.parentElement?.closest(interactiveSelector) &&
            !this.$el.querySelector(interactiveSelector)

        if (!this.isStandalone) {
            this.clearFeedback()
            this.liveRegion?.remove()

            return
        }

        if (this.isStandalone && !this.liveRegion?.isConnected) {
            this.liveRegion = document.createElement('span')
            this.liveRegion.className = 'fi-sr-only'
            this.liveRegion.setAttribute('role', 'status')
            this.liveRegion.setAttribute('aria-live', 'polite')
            this.$el.after(this.liveRegion)
        }
    },

    async copy(state, message, duration, failureMessage) {
        if (this.isCopying || this.isDestroyed) {
            return
        }

        this.isCopying = true
        this.clearFeedback()

        const clickHandler =
            this.$el.getAttribute('x-on:click') ??
            this.$el.getAttribute('x-on:click.prevent.stop')
        const isCurrent = () =>
            !this.isDestroyed &&
            this.$el.isConnected &&
            this.isStandalone &&
            clickHandler ===
                (this.$el.getAttribute('x-on:click') ??
                    this.$el.getAttribute('x-on:click.prevent.stop'))

        try {
            await window.navigator.clipboard.writeText(state)
        } catch {
            if (isCurrent()) {
                this.showFeedback(failureMessage, duration, isCurrent)
            }

            return
        } finally {
            this.isCopying = false
        }

        if (isCurrent()) {
            this.showFeedback(message, duration, isCurrent)
        }
    },

    showFeedback(message, duration, isCurrent) {
        this.updateStandalone()
        const feedback = tippy(this.$el, {
            content: message,
            trigger: 'manual',
            theme: this.$store.theme,
            aria: { content: null },
        })
        this.feedback = feedback
        feedback.show()
        const feedbackSequence = this.feedbackSequence

        this.$nextTick(() => {
            if (
                isCurrent() &&
                this.feedbackSequence === feedbackSequence &&
                this.liveRegion?.isConnected
            ) {
                this.liveRegion.textContent = message
            }
        })

        this.feedbackTimeout = setTimeout(() => this.clearFeedback(), duration)
    },

    clearFeedback() {
        this.feedbackSequence++
        clearTimeout(this.feedbackTimeout)
        this.feedback?.destroy()
        this.feedback = null

        if (this.liveRegion) {
            this.liveRegion.textContent = ''
        }
    },

    destroy() {
        this.isDestroyed = true
        this.observer?.disconnect()
        this.clearFeedback()
        this.liveRegion?.remove()
    },
})
