import noUiSlider from 'nouislider';
import wNumb from 'wnumb';

// Expose wNumb library to the window object
window.wNumb = wNumb;

export default function sliderFormComponent({
        state,
        statePath,
        range,
        step,
        start,
        margin,
        limit,
        connect,
        direction,
        orientation,
        behaviour,
        tooltips,
        format,
        pips,
        ariaFormat,
    }) {
    return {
        state,

        slider: null,

        init: function() {
            this.slider = noUiSlider.create(this.$el, {
                start: start,
                range: range,
                step: step,
                margin: margin,
                limit: limit,
                connect: connect,
                direction: direction,
                orientation: orientation,
                behaviour: behaviour,
                tooltips: tooltips,
                format: format,
                pips: pips,
                ariaFormat: ariaFormat
            })

            // Set the initial value of the slider
            if (![null, undefined, ''].includes(this.state)) {
                this.slider.set(this.state)
            }

            this.slider.on('update', (values, handle, unencoded, tap, positions, noUiSlider) => {
                this.state = this.slider.get(true) ?? null
                this.$nextTick(() => (this.isStateBeingUpdated = false))
            });
        },
    }
}
