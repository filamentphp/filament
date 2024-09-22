import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import AlpineLazyLoadAssets from 'alpine-lazy-load-assets'
import { md5 } from 'js-md5'
import Sortable from './sortable'
import Tooltip from '@ryangjchandler/alpine-tooltip'

import '../css/components/button.css'
import '../css/components/button-group.css'
import '../css/components/icon-button.css'
import '../css/components/pagination.css'
import '../css/components/tables/actions.css'
import '../css/components/tables/cell.css'
import '../css/components/tables/row.css'

import 'tippy.js/dist/tippy.css'
import 'tippy.js/themes/light.css'
import '../css/sortable.css'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(AlpineFloatingUI)
    window.Alpine.plugin(AlpineLazyLoadAssets)
    window.Alpine.plugin(Sortable)
    window.Alpine.plugin(Tooltip)
})

document.addEventListener('livewire:init', () => {
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        respond(() => {
            queueMicrotask(() => {
                if (component.effects.html) {
                    return
                }

                for (const [name, html] of Object.entries(
                    component.effects.partials ?? {},
                )) {
                    let el = component.el.querySelector(
                        '[wire\\:partial="' + name + '"]',
                    )

                    if (!el) {
                        continue
                    }

                    let wrapperTag = el.parentElement
                        ? // If the root element is a "tr", we need the wrapper to be a "table"...
                          el.parentElement.tagName.toLowerCase()
                        : 'div'

                    let wrapper = document.createElement(wrapperTag)

                    wrapper.innerHTML = html
                    let parentComponent

                    try {
                        parentComponent = closestComponent(el.parentElement)
                    } catch (exception) {}

                    parentComponent && (wrapper.__livewire = parentComponent)

                    let to = wrapper.firstElementChild

                    to.__livewire = component

                    window.Alpine.morph(el, to, {
                        updating: (el, toEl, childrenOnly, skip) => {
                            if (isntElement(el)) {
                                return
                            }

                            if (el.__livewire_ignore === true) {
                                return skip()
                            }

                            if (el.__livewire_ignore_self === true) {
                                childrenOnly()
                            }

                            if (
                                isComponentRootEl(el) &&
                                el.getAttribute('wire:id') !== component.id
                            ) {
                                return skip()
                            }

                            if (isComponentRootEl(el)) {
                                toEl.__livewire = component
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

        function closestComponent(el, strict = true) {
            let closestRoot = Alpine.findClosest(el, (i) => i.__livewire)

            if (!closestRoot) {
                if (strict)
                    throw 'Could not find Livewire component in DOM tree'

                return
            }

            return closestRoot.__livewire
        }
    })
})

// https://github.com/laravel/framework/blob/5299c22321c0f1ea8ff770b84a6c6469c4d6edec/src/Illuminate/Translation/MessageSelector.php#L15
const pluralize = function (text, number, variables) {
    function extract(segments, number) {
        for (const part of segments) {
            const line = extractFromString(part, number)

            if (line !== null) {
                return line
            }
        }
    }

    function extractFromString(part, number) {
        const matches = part.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s)

        if (matches === null || matches.length !== 3) {
            return null
        }

        const condition = matches[1]

        const value = matches[2]

        if (condition.includes(',')) {
            const [from, to] = condition.split(',', 2)

            if (to === '*' && number >= from) {
                return value
            } else if (from === '*' && number <= to) {
                return value
            } else if (number >= from && number <= to) {
                return value
            }
        }

        return condition == number ? value : null
    }

    function ucfirst(string) {
        return (
            string.toString().charAt(0).toUpperCase() +
            string.toString().slice(1)
        )
    }

    function replace(line, replace) {
        if (replace.length === 0) {
            return line
        }

        const shouldReplace = {}

        for (let [key, value] of Object.entries(replace)) {
            shouldReplace[':' + ucfirst(key ?? '')] = ucfirst(value ?? '')
            shouldReplace[':' + key.toUpperCase()] = value
                .toString()
                .toUpperCase()
            shouldReplace[':' + key] = value
        }

        Object.entries(shouldReplace).forEach(([key, value]) => {
            line = line.replaceAll(key, value)
        })

        return line
    }

    function stripConditions(segments) {
        return segments.map((part) =>
            part.replace(/^[\{\[]([^\[\]\{\}]*)[\}\]]/, ''),
        )
    }

    let segments = text.split('|')

    const value = extract(segments, number)

    if (value !== null && value !== undefined) {
        return replace(value.trim(), variables)
    }

    segments = stripConditions(segments)

    return replace(
        segments.length > 1 && number > 1 ? segments[1] : segments[0],
        variables,
    )
}

window.jsMd5 = md5
window.pluralize = pluralize
