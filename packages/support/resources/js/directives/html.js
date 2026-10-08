// Renders HTML that Filament generates on the server, such as icons and escaped
// labels. Unlike `x-html`, it does not initialize Alpine directives inside the
// inserted HTML, and it also works in Alpine's CSP build, which disables
// `x-html`. Only use it for trusted or sanitized HTML.
export default function (Alpine) {
    Alpine.directive(
        'filament-html',
        (el, { expression }, { effect, evaluateLater }) => {
            const evaluate = evaluateLater(expression)

            effect(
                () => {
                    evaluate((html) => {
                        // `mutateDom()` keeps Alpine's mutation observer from
                        // initializing the inserted HTML...
                        Alpine.mutateDom(() => {
                            el.innerHTML = html ?? ''
                        })
                    })
                },
                { priority: 'structural' },
            )
        },
    )
}
