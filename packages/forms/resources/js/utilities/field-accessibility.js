export function observeFieldAccessibility(element, update) {
    const source = element.previousElementSibling

    if (!source?.hasAttribute('data-field-accessibility')) {
        return null
    }

    const sync = () =>
        update(
            {
                'aria-describedby': source.getAttribute('aria-describedby'),
                'aria-invalid': source.getAttribute('aria-invalid'),
            },
            source,
        )

    sync()

    const observer = new MutationObserver(sync)
    observer.observe(source, {
        attributes: true,
        attributeFilter: [
            'aria-describedby',
            'aria-invalid',
            'data-preserve-filepond-description',
        ],
    })

    return observer
}

export function setFieldAccessibilityAttributes(element, attributes) {
    if (!element) {
        return
    }

    for (const [name, value] of Object.entries(attributes)) {
        if (value === null || value === undefined || value === false) {
            element.removeAttribute(name)
        } else {
            element.setAttribute(name, value)
        }
    }
}
