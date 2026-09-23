import tippy, { type Instance } from 'tippy.js'
import mousetrap from 'mousetrap'

export interface InteractiveOptions {
    tooltip?: string | null
    keyBindings?: string[]
    disabled?: boolean
}

// Share Mousetrap's module-lifetime singleton with no per-component document listeners.
const bindings = new Map<string, Map<HTMLElement, () => void>>()
let restoreStopCallback: (() => void) | undefined

export function interactive(element: HTMLElement, options: InteractiveOptions) {
    let tooltip: Instance | undefined
    let observer: MutationObserver | undefined
    let unregister = () => {}
    const escape = (event: KeyboardEvent) => {
        if (event.key === 'Escape' && tooltip?.state.isVisible) {
            tooltip.hide()
            event.stopImmediatePropagation()
        }
    }
    if (options.tooltip) {
        const theme = () =>
            document.documentElement.classList.contains('dark')
                ? 'dark'
                : 'light'
        tooltip = tippy(element, {
            content: String(options.tooltip),
            allowHTML: false,
            theme: theme(),
        })
        observer = new MutationObserver(() =>
            tooltip?.setProps({ theme: theme() }),
        )
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        })
        window.addEventListener('keydown', escape, true)
    }
    if (options.keyBindings?.length && !options.disabled) {
        if (!restoreStopCallback) {
            const shortcuts = mousetrap.bind([], () => {})
            const stopCallback = shortcuts.stopCallback
            shortcuts.stopCallback = function (
                event,
                element,
                combination,
                sequence?: string,
            ) {
                return bindings.has(sequence ?? combination)
                    ? false
                    : stopCallback.call(this, event, element, combination)
            }
            restoreStopCallback = () => {
                shortcuts.stopCallback = stopCallback
            }
        }
        const keys = [...new Set(options.keyBindings)]
        for (const key of keys) {
            let registrations = bindings.get(key)
            if (!registrations) {
                registrations = new Map()
                bindings.set(key, registrations)
                mousetrap.bind(key, (event) => {
                    const modal = [
                        ...document.querySelectorAll<HTMLElement>(
                            '[aria-modal="true"]',
                        ),
                    ].find(
                        (modal) => getComputedStyle(modal).display !== 'none',
                    )
                    const candidates = [
                        ...(bindings.get(key)?.entries() ?? []),
                    ].filter(
                        ([host]) =>
                            host.isConnected &&
                            (!modal || modal.contains(host)),
                    )
                    const activate = candidates[candidates.length - 1]?.[1]
                    if (activate) {
                        event.preventDefault()
                        activate()
                    }
                })
            }
            registrations.set(element, () => element.click())
        }
        unregister = () => {
            for (const key of keys) {
                const registrations = bindings.get(key)
                registrations?.delete(element)
                if (!registrations?.size) {
                    bindings.delete(key)
                    mousetrap.unbind(key)
                }
            }
            if (!bindings.size) {
                restoreStopCallback?.()
                restoreStopCallback = undefined
            }
        }
    }
    return () => {
        unregister()
        observer?.disconnect()
        window.removeEventListener('keydown', escape, true)
        tooltip?.destroy()
    }
}
