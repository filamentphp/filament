import Chart from 'chart.js/auto'

Chart.defaults.font.family = `var(--filament-widgets-chart-font-family)`
Chart.defaults.color = '#6b7280'

export default function chart({ cachedData, options, type }) {
    return {
        chart: null,

        init: function () {
            let chart = this.initChart()

            this.$el.addEventListener('updateChartData', async ({ data }) => {
                chart.data = this.applyColorToData(data)
                chart.update('resize')
            })

            this.$el.addEventListener('filterChartData', async ({ data }) => {
                chart.destroy()
                chart = this.initChart(data)
            })
        },

        initChart: function (data = null) {
            return (this.chart = new Chart(this.$refs.canvas, {
                type: type,
                data: this.applyColorToData(data ?? cachedData),
                options: options ?? {},
            }))
        },

        applyColorToData: function (data) {
            data.datasets.forEach((dataset, datasetIndex) => {
                if (!dataset.backgroundColor) {
                    data.datasets[datasetIndex].backgroundColor =
                        getComputedStyle(
                            this.$refs.backgroundColorElement,
                        ).color
                }

                if (!dataset.borderColor) {
                    data.datasets[datasetIndex].borderColor = getComputedStyle(
                        this.$refs.borderColorElement,
                    ).color
                }
            })

            return data
        },
    }
}
