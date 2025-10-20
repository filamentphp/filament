import noUiSlider from 'nouislider'

export default function sliderFormComponent({
    arePipsStepped,
    behavior,
    decimalPlaces,
    fillTrack,
    isDisabled,
    isRtl,
    isVertical,
    maxDifference,
    minDifference,
    maxValue,
    minValue,
    nonLinearPoints,
    pipsDensity,
    pipsFilter,
    pipsFormatter,
    pipsMode,
    pipsValues,
    rangePadding,
    state,
    step,
    tooltips,
}) {
    return {
        state,

        slider: null,

        init() {
            this.slider = noUiSlider.create(this.$el, {
                behaviour: behavior,
                direction: isRtl ? 'rtl' : 'ltr',
                connect: fillTrack,
                format: {
                    from: (value) => +value,
                    to: (value) =>
                        decimalPlaces !== null
                            ? +value.toFixed(decimalPlaces)
                            : value,
                },
                limit: maxDifference,
                margin: minDifference,
                orientation: isVertical ? 'vertical' : 'horizontal',
                padding: rangePadding,
                pips: pipsMode
                    ? {
                          density: pipsDensity ?? 10,
                          filter: pipsFilter,
                          format: pipsFormatter,
                          mode: pipsMode,
                          stepped: arePipsStepped,
                          values: pipsValues,
                      }
                    : null,
                range: {
                    min: minValue,
                    ...(nonLinearPoints ?? {}),
                    max: maxValue,
                },
                start: Alpine.raw(this.state),
                step,
                tooltips,
            })

            if (isDisabled) {
                this.slider.disable()
            }

            this.slider.on('change', (values) => {
                this.state = values.length > 1 ? values : values[0]
            })

            this.$watch('state', () => {
                this.slider.set(Alpine.raw(this.state))
            })
        },

        destroy() {
            this.slider.destroy()
            this.slider = null
        },
    }
}
