import 'vanilla-colorful/hex-color-picker'
import 'vanilla-colorful/hsl-string-color-picker'
import 'vanilla-colorful/rgb-string-color-picker'
import 'vanilla-colorful/rgba-string-color-picker'

import '../../css/components/color-picker.css'

export default (Alpine) => {
    Alpine.data('colorPickerFormComponent', ({
        isAutofocused,
        isDisabled,
        state,
    }) => {
        return {
            isOpen: false,

            state,

            init: function () {
                if (! (this.state === null || this.state === '')) {
                    this.setState(this.state)
                }

                if (isAutofocused) {
                    this.openPicker()
                }

                this.$refs.input.addEventListener('change', (event) => {
                    this.setState(event.target.value)
                });

                this.$refs.picker.addEventListener('color-changed', (event) => {
                    this.setState(event.detail.value)
                });
            },

            openPicker: function () {
                if (isDisabled) {
                    return
                }

                this.isOpen = true
            },

            closePicker: function () {
                this.isOpen = false
            },

            setState: function (value) {
                this.state = value

                this.$refs.input.value = value
                this.$refs.picker.color = value
            }
        }
    })
}
