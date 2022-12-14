import tippy, {followCursor} from 'tippy.js'

// Disclaimer: This file is modified from the original
// located at https://github.com/ryangjchandler/alpine-tooltip/blob/main/src/index.js

export default function (Alpine) {

    Alpine.magic('tooltip', el => {
        return (content, config = {}) => {

            const onHiddenCallback = config.hasOwnProperty('onHidden') ?
                function (instance) {
                    // The lifecycle hooks do not seem to care about the return
                    // value of this hook. We will hijack it, and if it returns
                    // false we will not destroy the instance.
                    if (config.onHidden(instance) !== false)
                        instance.destroy()
                } : function (instance) {
                    instance.destroy()
                }

            // Since delay doesn't do anything
            // when we spawn it ourselves, we can
            // use it to control the duration of
            // the tooltip
            const delay = (config.delay == null)
                ? 0
                : (Array.isArray(config.delay))
                    ? config.delay[0] + config.delay[1]
                    : Number.isInteger(config.delay)
                        ? config.delay
                        : 0

            const instance = tippy(el, {
                content,
                trigger: 'manual',
                ...config,
                onHidden: onHiddenCallback,
                delay
            })

            instance.show()

            setTimeout(() => instance.hide(), delay)
        }
    })

    Alpine.directive('tooltip', (el, {modifiers, expression}, {evaluateLater, effect}) => {
        const config = modifiers.length > 0
            ? buildConfigFromModifiers(modifiers)
            : {}

        if (!el.__x_tippy) {
            el.__x_tippy = tippy(el, config)
        }

        const enableTooltip = () => el.__x_tippy.enable();
        const disableTooltip = () => el.__x_tippy.disable();

        const setupTooltip = (content) => {
            if (!content) {
                disableTooltip()
            } else {
                enableTooltip()

                el.__x_tippy.setContent(content)
            }
        }

        if (modifiers.includes('raw')) {
            setupTooltip(expression)
        } else {
            const getContent = evaluateLater(expression)

            effect(() => {
                getContent(content => {
                    if (typeof content === 'object') {
                        el.__x_tippy.setProps(content)
                        enableTooltip()
                    } else {
                        setupTooltip(content)
                    }
                })
            })
        }
    })
}


function buildConfigFromModifiers(modifiers) {
    const config = {
        plugins: [],
    }

    const getModifierArgument = (modifier) => {
        return modifiers[modifiers.indexOf(modifier) + 1]
    }

    if (modifiers.includes('animation')) {
        config.animation = getModifierArgument('animation')
    }

    if (modifiers.includes('duration')) {
        config.duration = parseInt(getModifierArgument('duration'))
    }

    if (modifiers.includes('delay')) {
        const delay = getModifierArgument('delay')

        config.delay = delay.includes('-')
            ? delay.split('-').map(n => parseInt(n))
            : parseInt(delay)
    }

    if (modifiers.includes('cursor')) {
        config.plugins.push(followCursor)

        const next = getModifierArgument('cursor')

        if (['x', 'initial'].includes(next)) {
            config.followCursor = next === 'x' ? 'horizontal' : 'initial'
        } else {
            config.followCursor = true
        }
    }

    if (modifiers.includes('on')) {
        config.trigger = getModifierArgument('on')
    }

    if (modifiers.includes('arrowless')) {
        config.arrow = false
    }

    if (modifiers.includes('html')) {
        config.allowHTML = true
    }

    if (modifiers.includes('interactive')) {
        config.interactive = true
    }

    if (modifiers.includes('border') && config.interactive) {
        config.interactiveBorder = parseInt(getModifierArgument('border'))
    }

    if (modifiers.includes('debounce') && config.interactive) {
        config.interactiveDebounce = parseInt(getModifierArgument('debounce'))
    }

    if (modifiers.includes('max-width')) {
        config.maxWidth = parseInt(getModifierArgument('max-width'))
    }

    if (modifiers.includes('theme')) {
        config.theme = getModifierArgument('theme')
    }

    if (modifiers.includes('placement')) {
        config.placement = getModifierArgument('placement')
    }

    return config
}
