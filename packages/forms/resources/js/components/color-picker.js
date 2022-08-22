import 'vanilla-colorful/hex-color-picker'
import 'vanilla-colorful/hsl-string-color-picker'
import 'vanilla-colorful/rgb-string-color-picker'
import 'vanilla-colorful/rgba-string-color-picker'

import '../../css/components/color-picker.css'
import dayjs from 'dayjs/esm'

export default (Alpine) => {
    Alpine.data(
        'colorPickerFormComponent',
        ({ isAutofocused, isDisabled, state }) => {
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

                    this.$refs.panel.addEventListener(
                        'color-changed',
                        (event) => {
                            this.setState(event.detail.value)
                        },
                    )
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
            }
        },
    )
}
