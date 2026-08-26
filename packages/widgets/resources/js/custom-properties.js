// Chart.js paints a chart onto a bare `<canvas>`, so its shape is unreachable from a
// stylesheet. The `--chart-*` and `--stat-chart-*` custom properties bridge that
// gap, and this is how the chart components read them.
//
// An unset property reads back as an empty string, so every call passes the default it
// stands in for as a `fallback`. That keeps each default in one place, and leaves a value
// Chart.js cannot use to be ignored by Chart.js rather than second-guessed here.
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
