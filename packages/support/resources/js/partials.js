document.addEventListener('livewire:init', () => {
    const findClosestLivewireComponent = (el) => {
        let closestRoot = Alpine.findClosest(el, (i) => i.__livewire)

        if (!closestRoot) {
            throw 'Could not find Livewire component in DOM tree.'
        }

        return closestRoot.__livewire
    }

    Livewire.interceptMessage(({ message, onSuccess }) => {
        onSuccess(({ payload }) => {
            queueMicrotask(() => {
                if (payload.effects?.html) {
                    return
                }

                for (const [name, html] of Object.entries(
                    payload.effects?.partials ?? {},
                )) {
                    let els = Array.from(
                        message.component.el.querySelectorAll(
                            `[wire\\:partial="${name}"]`,
                        ),
                    ).filter(
                        (el) =>
                            findClosestLivewireComponent(el) ===
                            message.component,
                    )

                    if (!els.length) {
                        continue
                    }

                    if (els.length > 1) {
                        throw `Multiple elements found for partial [${name}].`
                    }

                    let el = els[0]

                    let wrapperTag = el.parentElement
                        ? // If the root element is a "tr", we need the wrapper to be a "table"...
                          el.parentElement.tagName.toLowerCase()
                        : 'div'

                    let wrapper = document.createElement(wrapperTag)

                    wrapper.innerHTML = html
                    wrapper.__livewire = message.component

                    let to = wrapper.firstElementChild

                    to.__livewire = message.component

                    let existingComponentsMap = {}

                    el.querySelectorAll('[wire\\:id]').forEach((comp) => {
                        existingComponentsMap[comp.getAttribute('wire:id')] =
                            comp
                    })

                    to.querySelectorAll('[wire\\:id]').forEach((child) => {
                        if (child.hasAttribute('wire:snapshot')) return

                        let wireId = child.getAttribute('wire:id')
                        let existingComponent = existingComponentsMap[wireId]

                        if (existingComponent) {
                            child.replaceWith(existingComponent.cloneNode(true))
                        }
                    })

                    window.Alpine.morph(el, to, {
                        updating: (el, toEl, childrenOnly, skip) => {
                            if (isntElement(el)) {
                                return
                            }

                            if (el.__livewire_replace === true) {
                                el.innerHTML = toEl.innerHTML
                            }

                            if (el.__livewire_replace_self === true) {
                                el.outerHTML = toEl.outerHTML

                                return skip()
                            }

                            if (el.__livewire_ignore === true) {
                                return skip()
                            }

                            if (el.__livewire_ignore_self === true) {
                                childrenOnly()
                            }

                            if (
                                isComponentRootEl(el) &&
                                el.getAttribute('wire:id') !==
                                    message.component.id
                            ) {
                                return skip()
                            }

                            if (isComponentRootEl(el)) {
                                toEl.__livewire = message.component
                            }
                        },

                        key: (el) => {
                            if (isntElement(el)) {
                                return
                            }

                            if (el.hasAttribute(`wire:key`)) {
                                return el.getAttribute(`wire:key`)
                            }

                            if (el.hasAttribute(`wire:id`)) {
                                return el.getAttribute(`wire:id`)
                            }

                            return el.id
                        },

                        lookahead: false,
                    })
                }
            })
        })

        function isntElement(el) {
            return typeof el.hasAttribute !== 'function'
        }

        function isComponentRootEl(el) {
            return el.hasAttribute('wire:id')
        }
    })
})
