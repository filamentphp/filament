import tippy, { type Instance } from 'tippy.js'
import mousetrap from 'mousetrap'

export interface InteractiveOptions {
    tooltip?: string | null
    keyBindings?: string[]
    disabled?: boolean
}

// Mousetrap has no listener teardown API. Own one module-lifetime engine,
// never the application's singleton or one document engine per component.
let shortcuts: Mousetrap.MousetrapInstance | undefined
const registrations = new Set<{ element: HTMLElement; keys: string[] }>()

function normalizeBinding(binding: string): string {
    const aliases: Record<string, string> = {
        option: 'alt',
        command: 'meta',
        return: 'enter',
        escape: 'esc',
        plus: '+',
        mod: /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? 'meta' : 'ctrl',
    }
    return binding
        .trim()
        .split(/\s+/)
        .map((combination) => {
            const keys = (
                combination === '+'
                    ? ['plus']
                    : combination.replace(/\+\+/g, '+plus').split('+')
            ).map((key) => aliases[key] ?? key)
            const key = keys.pop()
            return [...keys.sort(), key].join('+')
        })
        .join(' ')
}

function isVisible(element: HTMLElement): boolean {
    return (
        element.isConnected &&
        element.getClientRects().length > 0 &&
        !['hidden', 'collapse'].includes(getComputedStyle(element).visibility)
    )
}

function rebuildBindings() {
    shortcuts ??= new mousetrap()
    shortcuts.stopCallback = () => false
    // `unbind()` replaces sequence callbacks with no-ops but retains their
    // prefixes. Reset our entire callback table before rebuilding it.
    shortcuts.reset()
    const bindings = new Map<string, HTMLElement[]>()
    for (const { element, keys } of registrations) {
        for (const key of keys)
            bindings.set(key, [...(bindings.get(key) ?? []), element])
    }
    for (const [key, hosts] of bindings) {
        shortcuts.bind(key, (event) => {
            const modals = [
                ...document.querySelectorAll<HTMLElement>(
                    '[aria-modal="true"]',
                ),
            ].filter(isVisible)
            const modal = modals[modals.length - 1]
            const element = [...hosts]
                .reverse()
                .find(
                    (host) =>
                        isVisible(host) &&
                        !host.closest('[inert]') &&
                        !host.matches(':disabled, [aria-disabled="true"]') &&
                        (!modal || modal.contains(host)),
                )
            if (element) {
                event.preventDefault()
                element.click()
            }
        })
    }
}

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
            ignoreAttributes: true,
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
        const registration = {
            element,
            keys: [...new Set(options.keyBindings.map(normalizeBinding))],
        }
        registrations.add(registration)
        rebuildBindings()
        unregister = () => {
            if (registrations.delete(registration)) rebuildBindings()
        }
    }
    return () => {
        unregister()
        observer?.disconnect()
        window.removeEventListener('keydown', escape, true)
        tooltip?.destroy()
    }
}
