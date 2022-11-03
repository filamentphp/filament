import Chart from 'chart.js/auto'

export default function statsOverviewCardChart({ labels, values }) {
    return {
        chart: null,

        init: function () {
            this.initChart()
        },

        initChart: function () {
            return (this.chart = new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: getComputedStyle(
                                this.$refs.backgroundColorElement,
                            ).color,
                            borderColor: getComputedStyle(
                                this.$refs.borderColorElement,
                            ).color,
                            borderWidth: 2,
                            fill: 'start',
                            tension: 0.5,
                        },
                    ],
                },
                options: {
                    elements: {
                        point: {
                            radius: 0,
                        },
                    },
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                        },
                    },
                    tooltips: {
                        enabled: false,
                    },
                },
            }))
        },
    }
}
