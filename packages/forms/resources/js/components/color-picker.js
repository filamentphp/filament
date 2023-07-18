import 'vanilla-colorful/hex-color-picker.js'
import 'vanilla-colorful/hsl-string-color-picker.js'
import 'vanilla-colorful/rgb-string-color-picker.js'
import 'vanilla-colorful/rgba-string-color-picker.js'

export default function colorPickerFormComponent({
    isAutofocused,
    isDisabled,
    isLiveOnPickerClose,
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
            })

            if (isLiveOnPickerClose) {
                new MutationObserver(() => {
                    if (this.$refs.panel.style.display !== 'none') {
                        return
                    }

                    this.$wire.call('$refresh')
                }).observe(this.$refs.panel, {
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
    }
}
