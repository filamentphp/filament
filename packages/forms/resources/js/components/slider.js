import noUiSlider from 'nouislider';

export default function sliderFormComponent({ state, range, step, start, margin, limit, connect, direction, orientation }) {
    return {
        state,

        slider: null,

        init: function () {
            this.slider = document.getElementById('slider')

            noUiSlider.create(this.slider, {
                start: start ?? [0],
                range:  range ?? {'min': 0, 'max': 100},
                step: step,
                margin: margin,
                limit: limit,
                connect: connect,
                direction: direction,
                orientation: orientation,
            })
        },
    }
}


