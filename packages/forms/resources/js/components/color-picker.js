import 'vanilla-colorful/hex-color-picker.js'
import 'vanilla-colorful/hsl-string-color-picker.js'
import 'vanilla-colorful/rgb-string-color-picker.js'
import 'vanilla-colorful/rgba-string-color-picker.js'

export default function colorPickerFormComponent({
    isAutofocused,
    isDisabled,
    isLive,
    isLiveDebounced,
    isLiveOnBlur,
    liveDebounce,
    state,
}) {
    let isDestroyed = false

    return {
        state,

        panelObserver: null,

        init() {
            if (!(this.state === null || this.state === '')) {
                this.setState(this.state)
            }

            if (isAutofocused) {
                this.togglePanelVisibility(this.$refs.input)
            }

            this.$watch(
                'state',
                Alpine.debounce((value) => {
                    if (isDestroyed) {
                        return
                    }

                    if (!CSS.supports('color', value)) {
                        return
                    }

                    this.$refs.picker.color = value
                }, 200),
            )

            this.$refs.input.addEventListener('change', (event) => {
                this.setState(event.target.value)
            })

            this.$refs.panel.addEventListener('color-changed', (event) => {
                this.setState(event.detail.value)

                if (isLiveOnBlur || !(isLive || isLiveDebounced)) {
                    return
                }

                setTimeout(
                    () => {
                        if (isDestroyed) {
                            return
                        }

                        if (this.state !== event.detail.value) {
                            return
                        }

                        this.commitState()
                    },
                    isLiveDebounced ? liveDebounce : 250,
                )
            })

            if (isLive || isLiveDebounced || isLiveOnBlur) {
                this.panelObserver = new MutationObserver(() =>
                    this.isOpen() ? null : this.commitState(),
                )

                this.panelObserver.observe(this.$refs.panel, {
                    attributes: true,
                    childList: true,
                })
            }
        },

        togglePanelVisibility() {
            if (isDisabled) {
                return
            }

            this.$refs.panel.toggle(this.$refs.input)
        },

        setState(value) {
            this.state = value

            this.$refs.input.value = value
            this.$refs.picker.color = value
        },

        isOpen() {
            return this.$refs.panel.style.display === 'block'
        },

        commitState() {
            if (isDestroyed) {
                return
            }

            if (
                JSON.stringify(this.$wire.__instance.canonical) ===
                JSON.stringify(this.$wire.__instance.ephemeral)
            ) {
                return
            }

            this.$wire.$commit()
        },

        destroy() {
            isDestroyed = true
            this.panelObserver?.disconnect()
        },
    }
}
