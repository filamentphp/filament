import noUiSlider from 'nouislider';

export default function sliderFormComponent({ state }) {
    return {
        state,

        slider: null,

        init: function () {
            this.slider = document.getElementById('slider')

            console.log(this.slider);

            noUiSlider.create(this.slider, {
                start: [20, 80],
                connect: true,
                range: {
                    min: 0,
                    max: 100,
                },
            })
        },
    }
}

