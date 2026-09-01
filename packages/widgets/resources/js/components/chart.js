import Chart from 'chart.js/auto'
import { color } from 'chart.js/helpers'
import 'chartjs-adapter-luxon'
import readCustomProperties from '../custom-properties'

const darken = (value, amount) => {
    if (Array.isArray(value)) {
        return value.map((entry) => darken(entry, amount))
    }

    const parsed = color(value)

    return parsed.valid ? parsed.darken(amount).rgbString() : value
}

if (
    window.filamentChartJsGlobalPlugins &&
    Array.isArray(window.filamentChartJsGlobalPlugins) &&
    window.filamentChartJsGlobalPlugins.length > 0
) {
    Chart.register(...window.filamentChartJsGlobalPlugins)
}

Chart.defaults.plugins.legend.position = 'bottom'

export default function chart({ cachedData, options, type }) {
    return {
        userBackgroundColor: options?.backgroundColor,
        userBorderColor: options?.borderColor,
        userTextColor: options?.color,
        userPointBackgroundColor: options?.pointBackgroundColor,
        userXGridColor: options?.scales?.x?.grid?.color,
        userYGridColor: options?.scales?.y?.grid?.color,
        userRadialGridColor: options?.scales?.r?.grid?.color,
        userRadialTicksColor: options?.scales?.r?.ticks?.color,
        userTooltipBackgroundColor: options?.plugins?.tooltip?.backgroundColor,
        userTooltipTitleColor: options?.plugins?.tooltip?.titleColor,
        userTooltipBodyColor: options?.plugins?.tooltip?.bodyColor,
        userTooltipBorderColor: options?.plugins?.tooltip?.borderColor,
        userLineTension: options?.tension,
        userPointStyle: options?.pointStyle,
        userBarBorderRadius: options?.borderRadius,
        userTooltipCornerRadius: options?.plugins?.tooltip?.cornerRadius,
        userTooltipBorderWidth: options?.plugins?.tooltip?.borderWidth,
        userLegendBorderRadius: options?.plugins?.legend?.labels?.borderRadius,
        userLegendUseBorderRadius:
            options?.plugins?.legend?.labels?.useBorderRadius,
        userLegendBoxWidth: options?.plugins?.legend?.labels?.boxWidth,
        userBorderWidth: options?.borderWidth,
        userPointRadius: options?.pointRadius,

        init() {
            this.$wire.$on('updateChartData', ({ data }) =>
                this.updateChartData(data),
            )

            Alpine.effect(() => {
                Alpine.store('theme')

                this.$nextTick(() => this.updateChartTheme())
            })

            this.systemThemeMediaQuery = window.matchMedia(
                '(prefers-color-scheme: dark)',
            )
            this.systemThemeListener = () => {
                if (Alpine.store('theme') !== 'system') {
                    return
                }

                this.$nextTick(() => this.updateChartTheme())
            }
            this.systemThemeMediaQuery.addEventListener(
                'change',
                this.systemThemeListener,
            )

            // Defer `initChart()` to `$nextTick` so the `Alpine.effect` above runs its
            // mandatory first invocation before the chart exists. `updateChartTheme()` then
            // exits early on that first run; otherwise the effect would tear down and
            // recreate the chart on every mount.
            this.$nextTick(() => {
                this.initChart()

                this.resizeObserver = new ResizeObserver(() =>
                    this.getChart()?.resize(),
                )
                this.resizeObserver.observe(this.$el)

                this.dprChangeHandler = Alpine.debounce(
                    () => this.handleDprChange(),
                    250,
                )
                window.addEventListener('resize', this.dprChangeHandler)
            })
        },

        initChart() {
            if (
                !this.$refs.canvas ||
                !this.$refs.backgroundColorElement ||
                !this.$refs.borderColorElement ||
                !this.$refs.textColorElement ||
                !this.$refs.gridColorElement
            ) {
                return
            }

            const fontFamily = getComputedStyle(this.$el).fontFamily
            const hasMaxHeight = this.$refs.canvas.style.maxHeight !== '100%'

            options ??= {}
            options.animation ??= {}
            options.animation.duration ??= 0
            options.font ??= {}
            options.font.family ??= fontFamily
            options.responsive ??= false
            options.maintainAspectRatio ??= hasMaxHeight
            options.pointHitRadius ??= 4
            options.scales ??= {}
            options.scales.x ??= {}
            options.scales.x.border ??= {}
            options.scales.x.border.display ??= false
            options.scales.x.grid ??= {}
            options.scales.x.grid.display ??= false
            options.scales.y ??= {}
            options.scales.y.border ??= {}
            options.scales.y.border.display ??= false
            options.scales.y.grid ??= {}

            if (['doughnut', 'pie', 'polarArea'].includes(type)) {
                options.scales.x.display ??= false
                options.scales.y.display ??= false
                options.scales.y.grid.display ??= false

                options.elements ??= {}
                options.elements.arc ??= {}

                options.plugins ??= {}
                options.plugins.legend ??= {}
                options.plugins.legend.labels ??= {}
                options.plugins.legend.labels.generateLabels ??= (chart) => {
                    const labels =
                        Chart.overrides[
                            type
                        ].plugins.legend.labels.generateLabels(chart)

                    for (const label of labels) {
                        label.strokeStyle = darken(label.fillStyle, 0.2)
                    }

                    return labels
                }
            }

            if (type === 'polarArea') {
                options.scales.r ??= {}
                options.scales.r.grid ??= {}
                options.scales.r.ticks ??= {}
                options.scales.r.ticks.backdropColor ??= 'transparent'
            }

            this.applyChartColors(options)
            this.applyChartCustomProperties(options)
            this.normalizeDatasets(cachedData)

            new Chart(this.$refs.canvas, {
                type,
                data: cachedData,
                options,
                plugins: window.filamentChartJsPlugins ?? [],
            })
        },

        updateChartData(newData) {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            this.normalizeDatasets(newData)
            chart.data = newData
            chart.update('resize')
        },

        normalizeDatasets(data) {
            for (const dataset of data?.datasets ?? []) {
                if (type === 'bar' && dataset.backgroundColor !== undefined) {
                    dataset.borderColor ??= darken(dataset.backgroundColor, 0.2)
                    dataset.hoverBorderColor ??= darken(
                        dataset.backgroundColor,
                        0.3,
                    )
                }

                if (
                    type === 'scatter' &&
                    dataset.backgroundColor !== undefined
                ) {
                    dataset.borderColor ??= dataset.backgroundColor
                }

                if (
                    ['line', 'scatter'].includes(type) &&
                    dataset.borderColor !== undefined
                ) {
                    dataset.pointBackgroundColor ??= dataset.borderColor
                }
            }
        },

        updateChartTheme() {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            this.applyChartColors(chart.options)
            this.applyChartCustomProperties(chart.options)

            // Not `'none'`: that is one of Chart.js' "direct update" modes, which reuse a
            // cached snapshot of the options shared between a dataset's elements instead of
            // refreshing it. Every per-element option - bar and arc colors, point colors,
            // point radius and style - would keep the value it was built with. `'resize'`
            // refreshes them, and its transition is already zero-duration, so nothing
            // animates. It is the mode `updateChartData()` uses for the same reason.
            chart.update('resize')
        },

        applyChartColors(options) {
            const {
                backgroundColor,
                borderColor,
                textColor,
                gridColor,
                tooltipBackgroundColor,
                tooltipTextColor,
                tooltipBorderColor,
            } = this.getChartColors()

            const resolvedBorderColor = this.userBorderColor ?? borderColor

            options.backgroundColor =
                this.userBackgroundColor ?? backgroundColor
            options.borderColor = resolvedBorderColor
            options.color = this.userTextColor ?? textColor
            options.pointBackgroundColor =
                this.userPointBackgroundColor ?? resolvedBorderColor

            options.elements ??= {}
            options.elements.bar ??= {}
            options.elements.bar.borderColor = resolvedBorderColor

            // The tooltip color elements arrived after the others, so a published copy of
            // the view may not have them; without them Chart.js keeps its own colors.
            if (this.$refs.tooltipBackgroundColorElement) {
                options.plugins ??= {}
                options.plugins.tooltip ??= {}
                options.plugins.tooltip.backgroundColor =
                    this.userTooltipBackgroundColor ?? tooltipBackgroundColor
                options.plugins.tooltip.titleColor =
                    this.userTooltipTitleColor ?? tooltipTextColor
                options.plugins.tooltip.bodyColor =
                    this.userTooltipBodyColor ?? tooltipTextColor
                options.plugins.tooltip.borderColor =
                    this.userTooltipBorderColor ?? tooltipBorderColor
            }

            if (['doughnut', 'pie', 'polarArea'].includes(type)) {
                options.elements.arc ??= {}
                options.elements.arc.borderColor =
                    this.getSurroundingBackgroundColor()
            }

            options.scales.x.grid.color = this.userXGridColor ?? gridColor
            options.scales.y.grid.color = this.userYGridColor ?? gridColor

            if (type === 'polarArea') {
                options.scales.r.grid.color =
                    this.userRadialGridColor ?? gridColor
                options.scales.r.ticks.color =
                    this.userRadialTicksColor ?? textColor
            }
        },

        handleDprChange() {
            const chart = this.getChart()

            if (!chart) {
                return
            }

            if (chart.currentDevicePixelRatio !== window.devicePixelRatio) {
                chart.resize()
            }
        },

        getChart() {
            if (!this.$refs.canvas) {
                return null
            }

            return Chart.getChart(this.$refs.canvas)
        },

        getSurroundingBackgroundColor() {
            let element = this.$el.parentElement

            while (element) {
                const backgroundColor =
                    getComputedStyle(element).backgroundColor

                if (
                    backgroundColor &&
                    backgroundColor !== 'rgba(0, 0, 0, 0)' &&
                    backgroundColor !== 'transparent'
                ) {
                    return backgroundColor
                }

                element = element.parentElement
            }

            return '#ffffff'
        },

        getChartColors() {
            const sentinelColor = (element) =>
                element ? getComputedStyle(element).color : null

            return {
                backgroundColor: getComputedStyle(
                    this.$refs.backgroundColorElement,
                ).color,
                borderColor: getComputedStyle(this.$refs.borderColorElement)
                    .color,
                textColor: getComputedStyle(this.$refs.textColorElement).color,
                gridColor: getComputedStyle(this.$refs.gridColorElement).color,
                tooltipBackgroundColor: sentinelColor(
                    this.$refs.tooltipBackgroundColorElement,
                ),
                tooltipTextColor: sentinelColor(
                    this.$refs.tooltipTextColorElement,
                ),
                tooltipBorderColor: sentinelColor(
                    this.$refs.tooltipBorderColorElement,
                ),
            }
        },

        applyChartCustomProperties(options) {
            const {
                lineTension,
                pointStyle,
                barBorderRadius,
                tooltipCornerRadius,
                tooltipBorderWidth,
                legendBorderRadius,
                legendBoxWidth,
                borderWidth,
                pointRadius,
            } = this.getChartCustomProperties()

            options.borderWidth = this.userBorderWidth ?? borderWidth
            options.tension = this.userLineTension ?? lineTension
            options.pointStyle = this.userPointStyle ?? pointStyle
            options.pointRadius = this.userPointRadius ?? pointRadius

            if (type === 'bar') {
                options.borderRadius =
                    this.userBarBorderRadius ?? barBorderRadius
            }

            options.plugins ??= {}
            options.plugins.tooltip ??= {}
            options.plugins.tooltip.cornerRadius =
                this.userTooltipCornerRadius ?? tooltipCornerRadius
            options.plugins.tooltip.borderWidth =
                this.userTooltipBorderWidth ?? tooltipBorderWidth

            const resolvedLegendBorderRadius =
                this.userLegendBorderRadius ?? legendBorderRadius

            options.plugins.legend ??= {}
            options.plugins.legend.labels ??= {}
            options.plugins.legend.labels.borderRadius =
                resolvedLegendBorderRadius
            // Chart.js ignores the radius unless this is on, and treats a radius of `0` as
            // "inherit from the data" rather than "square", so it is derived rather than
            // exposed as a property of its own.
            options.plugins.legend.labels.useBorderRadius =
                this.userLegendUseBorderRadius ?? resolvedLegendBorderRadius > 0
            options.plugins.legend.labels.boxWidth =
                this.userLegendBoxWidth ?? legendBoxWidth
        },

        // The parts of a chart that a stylesheet cannot otherwise reach, since Chart.js
        // paints them onto a bare `<canvas>`. This covers the static appearance of what is
        // painted, including everything Filament pins to a value of its own; how a chart
        // behaves - animation, hit areas, which scales it shows - belongs in `getOptions()`.
        // Each fallback is the default it stands in for: Chart.js' own, except where the
        // panel's design language asks for something else. Bars and legend swatches are
        // slightly rounded, since nothing else in Filament has square corners, and the
        // tooltip's `4` is the radius `tippy.js` gives every other tooltip in a panel.
        getChartCustomProperties() {
            const { number, keyword } = readCustomProperties(this.$el)

            return {
                lineTension: number('--chart-line-tension', 0),
                pointStyle: keyword('--chart-point-style', 'circle'),
                barBorderRadius: number('--chart-bar-border-radius', 2),
                tooltipCornerRadius: number('--chart-tooltip-corner-radius', 4),
                tooltipBorderWidth: number('--chart-tooltip-border-width', 0),
                legendBorderRadius: number('--chart-legend-border-radius', 2),
                legendBoxWidth: number('--chart-legend-box-width', 12),
                borderWidth: number('--chart-border-width', 2),
                pointRadius: number('--chart-point-radius', 2),
            }
        },

        destroy() {
            this.resizeObserver?.disconnect()
            this.dprChangeHandler &&
                window.removeEventListener('resize', this.dprChangeHandler)
            this.systemThemeMediaQuery?.removeEventListener(
                'change',
                this.systemThemeListener,
            )
            this.getChart()?.destroy()
        },
    }
}
