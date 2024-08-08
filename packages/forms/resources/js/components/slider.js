import noUiSlider from 'nouislider';
import wNumb from 'wnumb';

// Expose wNumb library to the window object
window.wNumb = wNumb;

export default function sliderFormComponent({ state, range, step, start, margin, limit, connect, direction, orientation, behaviour, tooltips, format, pips, ariaFormat }) {
    return {
        state,

        slider: null,

        init: function () {
            noUiSlider.create(this.$el, {
                start: start ?? [0],
                range:  range ?? {'min': 0, 'max': 100},
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
        },
    }
}



