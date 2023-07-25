import Chart from 'chart.js/auto'

export default function chart({ cachedData, options, type }) {
    return {
        init: function () {
            this.initChart()

            this.$wire.$on('updateChartData', ({ data }) => {
                chart = this.getChart()
                chart.data = data
                chart.update('resize')
            })

            Alpine.effect(() => {
                Alpine.store('theme')

                this.getChart().destroy()
                this.initChart()
            })

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', () => {
                    if (Alpine.store('theme') !== 'system') {
                        return
                    }

                    this.$nextTick(() => {
                        this.getChart().destroy()
                        this.initChart()
                    })
                })
        },

        initChart: function (data = null) {
            Chart.defaults.backgroundColor = getComputedStyle(
                this.$refs.backgroundColorElement,
            ).color

            Chart.defaults.borderColor = getComputedStyle(
                this.$refs.borderColorElement,
            ).color

            Chart.defaults.color = getComputedStyle(
                this.$refs.colorElement,
            ).color

            Chart.defaults.font.family = getComputedStyle(this.$el).fontFamily

            return new Chart(this.$refs.canvas, {
                type: type,
                data: data ?? cachedData,
                options: {
                    animation: {
                        duration: 0,
                    },
                    ...options,
                },
            })
        },

        getChart: function () {
            return Chart.getChart(this.$refs.canvas)
        },
    }
}
