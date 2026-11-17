<script>
    window.rendererReadiness = []
    const observedRendererHosts = new WeakSet()
    new MutationObserver((records) => {
        for (const { target } of records) {
            const host = target.querySelector(':scope > [x-ref="host"]')
            if (
                !host ||
                target.hasAttribute('aria-hidden') ||
                observedRendererHosts.has(host)
            )
                continue
            observedRendererHosts.add(host)
            window.rendererReadiness.push(host.childElementCount > 0)
        }
    }).observe(document.documentElement, {
        subtree: true,
        attributes: true,
        attributeFilter: ['aria-hidden'],
    })
</script>
