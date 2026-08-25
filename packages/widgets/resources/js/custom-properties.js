// Chart.js paints a chart onto a bare `<canvas>`, so its shape is unreachable from a
// stylesheet. The `--chart-*` and `--stat-chart-*` custom properties bridge that
// gap, and this is how the chart components read them.
//
// Each property is registered with `@property` in the widget's CSS, which gives it a type
// and an initial value matching the default it is standing in for. The `fallback`
// arguments here cover a theme that was compiled before those registrations existed.
export default function readCustomProperties(element) {
    const styles = getComputedStyle(element)

    const read = (property) => styles.getPropertyValue(property).trim()

    return {
        number: (property, fallback) => {
            const value = parseFloat(read(property))

            return Number.isNaN(value) ? fallback : value
        },

        // Chart.js switches a feature off with boolean `false`, and a custom property can
        // only ever yield the string `'false'`, which is truthy — hence the `none` keyword.
        keyword: (property, fallback) => {
            const value = read(property) || fallback

            return value === 'none' ? false : value
        },
    }
}
