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
    return {
        state,

        init: function () {
            if (!(this.state === null || this.state === '')) {
                this.setState(this.state)
            }

            if (isAutofocused) {
                this.togglePanelVisibility(this.$refs.input)
            }

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
                        if (this.state !== event.detail.value) {
                            return
                        }

                        this.commitState()
                    },
                    isLiveDebounced ? liveDebounce : 250,
                )
            })

            if (isLive || isLiveDebounced || isLiveOnBlur) {
                new MutationObserver(() =>
                    this.isOpen() ? null : this.commitState(),
                ).observe(this.$refs.panel, {
                    attributes: true,
                    childList: true,
                })
            }
        },

        togglePanelVisibility: function () {
            if (isDisabled) {
                return
            }

            this.$refs.panel.toggle(this.$refs.input)
        },

        setState: function (value) {
            this.state = value

            this.$refs.input.value = value
            this.$refs.panel.color = value
        },

        isOpen: function () {
            return this.$refs.panel.style.display === 'block'
        },

        commitState: function () {
            if (
                JSON.stringify(this.$wire.__instance.canonical) ===
                JSON.stringify(this.$wire.__instance.ephemeral)
            ) {
                return
            }

            this.$wire.$commit()
        },
    }
}
