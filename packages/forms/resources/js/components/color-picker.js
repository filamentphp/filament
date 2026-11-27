import 'vanilla-colorful/hex-color-picker.js'
import 'vanilla-colorful/hsl-string-color-picker.js'
import 'vanilla-colorful/rgb-string-color-picker.js'
import 'vanilla-colorful/rgba-string-color-picker.js'

export default function colorPickerFormComponent({
    format,
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
                this.selectColor(event.detail.value)
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

            this.$dispatch('async-component-ready')

            if (isAutofocused) {
                this.$nextTick(() => {
                    if (!isDestroyed) {
                        this.togglePanelVisibility()
                    }
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

        selectColor(value) {
            this.setState(value)

            if (isLiveOnBlur || !(isLive || isLiveDebounced)) {
                return
            }

            setTimeout(
                () => {
                    if (isDestroyed) {
                        return
                    }

                    if (this.state !== value) {
                        return
                    }

                    this.commitState()
                },
                isLiveDebounced ? liveDebounce : 250,
            )
        },

        isEyeDropperSupported() {
            return 'EyeDropper' in window
        },

        async openEyeDropper() {
            let color

            try {
                color = (await new EyeDropper().open()).sRGBHex
            } catch {
                return
            }

            if (isDestroyed) {
                return
            }

            if (!this.isOpen()) {
                this.$refs.panel.open(this.$refs.input)
            }

            this.selectColor(this.formatHexColor(color))
        },

        formatHexColor(color) {
            if (format === 'hex') {
                return color
            }

            const [red, green, blue] = [1, 3, 5].map((index) =>
                parseInt(color.slice(index, index + 2), 16),
            )

            if (format === 'rgb') {
                return `rgb(${red}, ${green}, ${blue})`
            }

            if (format === 'rgba') {
                return `rgba(${red}, ${green}, ${blue}, 1)`
            }

            const max = Math.max(red, green, blue) / 255
            const min = Math.min(red, green, blue) / 255
            const lightness = (max + min) / 2
            const delta = max - min

            let hue = 0

            if (delta !== 0) {
                hue =
                    max === red / 255
                        ? ((green - blue) / 255 / delta) % 6
                        : max === green / 255
                          ? (blue - red) / 255 / delta + 2
                          : (red - green) / 255 / delta + 4
            }

            const saturation =
                delta === 0 ? 0 : delta / (1 - Math.abs(2 * lightness - 1))

            return `hsl(${Math.round((hue * 60 + 360) % 360)}, ${Math.round(saturation * 100)}%, ${Math.round(lightness * 100)}%)`
        },

        isOpen() {
            return this.$refs.panel.style.display === 'block'
        },

        closePanelAndRestoreFocus() {
            this.$refs.panel.close()

            if (document.activeElement === this.$refs.input) {
                return
            }

            const preventPanelOpening = (event) => {
                event.stopImmediatePropagation()
            }

            this.$refs.input.addEventListener(
                'focus',
                preventPanelOpening,
                true,
            )
            this.$refs.input.focus()
            this.$refs.input.removeEventListener(
                'focus',
                preventPanelOpening,
                true,
            )
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
            this.$refs.panel?.close?.()
        },
    }
}
