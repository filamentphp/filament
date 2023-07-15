import Chart from 'chart.js/auto'

const defaultColor = '107, 114, 128';

Chart.defaults.font.family = `var(--filament-widgets-chart-font-family)`
Chart.defaults.color = `rgba(${defaultColor}, 0.8)`

export default function chart({ cachedData, options, type }) {
    return {
        chart: null,

        init: function () {
            Chart.defaults.backgroundColor = getComputedStyle(this.$refs.backgroundColorElement).color;
            Chart.defaults.borderColor = getComputedStyle(this.$refs.borderColorElement).color

            let chart = this.initChart()

            this.$el.addEventListener('updateChartData', async ({ data }) => {
                chart.data = data
                chart.update('resize')
            })

            this.$el.addEventListener('filterChartData', async ({ data }) => {
                chart.destroy()
                chart = this.initChart(data)
            })

            window.addEventListener('theme-changed', () => {

                Chart.defaults.backgroundColor = getComputedStyle(this.$refs.backgroundColorElement).color;
                Chart.defaults.borderColor = getComputedStyle(this.$refs.borderColorElement).color

                const data = this.getChart().data

                chart.destroy()
                chart = this.initChart(data)
            })
        },

        initChart: function (data = null) {
            let theme = localStorage.getItem('theme') ?? 'light';

            return (this.chart = new Chart(this.$refs.canvas, {
                type: type,
                data: data ?? cachedData,
                options: { scales: {
                    y: {
                        grid: {
                            color: theme === 'light' ? `rgba(${defaultColor}, 0.5)` : `rgba(${defaultColor}, 0.2)`
                        },
                        border: {
                            color: theme === 'light' ? `rgba(${defaultColor}, 0.5)` : `rgba(${defaultColor}, 1)`
                        },
                    },
                    x: {
                        grid: {
                            color: theme === 'light' ? `rgba(${defaultColor}, 0.5)` : `rgba(${defaultColor}, 0.2)`
                        },
                        border: {
                            color: theme === 'light' ? `rgba(${defaultColor}, 0.5)` : `rgba(${defaultColor}, 1)`
                        }
                    }
                }, ...options},
            }))
        },

        getChart: function () {
            return Chart.getChart(this.$refs.canvas)
        },
    }
}
