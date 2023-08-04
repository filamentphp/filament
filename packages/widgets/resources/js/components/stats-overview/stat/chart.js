import Chart from 'chart.js/auto'

export default function statsOverviewStatChart({ labels, values }) {
    return {
        init: function () {

            Alpine.effect(() => {
                Alpine.store('theme')
                if(this.getChart()) {
                    this.getChart().destroy()
                }
                
                this.initChart()
            })

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', () => {
                    if (Alpine.store('theme') !== 'system') {
                        return
                    }

                    this.$nextTick(() => {
                        if(this.getChart()) {
                            this.getChart().destroy()
                        }
                        
                        this.initChart()
                    })
                })
        },

        initChart: function () {
            Chart.defaults.backgroundColor = getComputedStyle(
                this.$refs.backgroundColorElement,
            ).color

            Chart.defaults.borderColor = getComputedStyle(
                this.$refs.borderColorElement,
            ).color

            return new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: values,
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
            })
        },

        getChart: function () {
            return Chart.getChart(this.$refs.canvas)
        },
    }
}
