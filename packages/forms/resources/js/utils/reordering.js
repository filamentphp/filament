export default function reordering({
    schemaComponent,
    moveUpAction,
    moveDownAction,
    reorderAction,
    message,
    hasCustomReorderAction,
}) {
    const getActionIdentity = (action) =>
        JSON.stringify({
            name: action?.name,
            arguments: action?.arguments,
            context: action?.context,
        })

    return {
        pending: null,
        frame: null,
        destroyed: false,
        listeners: [],
        removeCommitHook: null,

        init() {
            const listen = (element, name, callback, capture = true) => {
                element.addEventListener(name, callback, capture)
                this.listeners.push(() =>
                    element.removeEventListener(name, callback, capture),
                )
            }

            listen(document, 'pointerdown', (event) => {
                if (!this.pending?.control?.contains(event.target))
                    this.relinquishFocus()
            })
            listen(document, 'keydown', (event) => {
                if (event.key === 'Tab') this.relinquishFocus()
            })
            listen(document, 'focusin', (event) => {
                if (this.pending && event.target !== this.pending.control) {
                    this.relinquishFocus()
                }
            })

            listen(this.$root, 'click', (event) => {
                const control = event.target.closest('[data-reorder-direction]')
                if (!control || !this.owns(control)) return
                if (this.pending) {
                    event.preventDefault()
                    event.stopImmediatePropagation()
                    return
                }
                // Links, native confirmations, and custom handlers retain their own interaction contract.
                if (
                    !control.dataset.reorderAction?.startsWith(
                        'mountAction(',
                    ) ||
                    control.getAttribute('wire:click') !==
                        control.dataset.reorderAction ||
                    control
                        .getAttributeNames()
                        .some(
                            (name) =>
                                name === 'wire:confirm' ||
                                name.startsWith('wire:confirm.'),
                        ) ||
                    control.disabled
                )
                    return
                this.begin(
                    control
                        .closest('[x-sortable-item]')
                        .getAttribute('x-sortable-item'),
                    control.dataset.reorderDirection,
                    control,
                )
            })

            listen(this.$root, 'start', (event) => {
                if (event.target !== this.$refs.reorderItems) return
                this.begin(event.item.getAttribute('x-sortable-item'), 'drag')
            })
            listen(this.$root, 'end', (event) => {
                if (event.target !== this.$refs.reorderItems) return
                if (event.oldDraggableIndex === event.newDraggableIndex) {
                    this.finish()
                    event.stopImmediatePropagation()
                    return
                }
                this.setSortingDisabled(true)
            })

            this.removeCommitHook = Livewire.hook(
                'commit',
                ({ component, commit, succeed, fail }) => {
                    const operation = this.pending
                    if (component.id !== this.$wire.$id) return
                    const partialTargets = []
                    for (
                        let element = this.$root;
                        element;
                        element = element.parentElement
                    ) {
                        if (
                            element.hasAttribute('wire:id') &&
                            element.getAttribute('wire:id') !== component.id
                        )
                            break
                        const target = element.getAttribute('wire:partial')
                        if (target) partialTargets.push(target)
                        if (element === component.el) break
                    }
                    const rendersOwner = (effects) =>
                        effects.html ||
                        partialTargets.some(
                            (target) => effects.partials?.[target],
                        )
                    succeed(({ effects }) => {
                        if (this.pending !== operation) return
                        if (
                            operation &&
                            (!operation.synchronizing || !rendersOwner(effects))
                        )
                            return
                        this.schedule(() => {
                            if (this.destroyed || !this.$root.isConnected)
                                return
                            this.updateHandles()
                            if (
                                operation?.synchronizing &&
                                this.isCurrent(operation)
                            ) {
                                this.finish()
                            }
                        })
                    })
                    if (!operation) return
                    if (operation.synchronizing) return
                    const mounted = component.canonical.mountedActions ?? []
                    const matches = (action) =>
                        action?.context?.schemaComponent === schemaComponent &&
                        action.name === operation.action &&
                        (operation.direction === 'drag' ||
                            String(action.arguments?.item) === operation.item)
                    const isDescendantAction =
                        mounted.some(matches) && !matches(mounted.at(-1))

                    const matched = commit.calls.some((call) => {
                        if (call.method === 'mountAction') {
                            return matches({
                                name: call.params[0],
                                arguments: call.params[1],
                                context: call.params[2],
                            })
                        }
                        return (
                            ['callMountedAction', 'unmountAction'].includes(
                                call.method,
                            ) && mounted.some(matches)
                        )
                    })
                    if (!matched) return

                    const cancelling = commit.calls.some(
                        (call) => call.method === 'unmountAction',
                    )
                    succeed(({ effects }) => {
                        if (!this.isCurrent(operation)) return
                        const currentMountedActions =
                            component.canonical.mountedActions ?? []
                        const stillMounted = currentMountedActions.some(matches)
                        for (const [
                            index,
                            action,
                        ] of operation.reopenedModals ?? []) {
                            if (
                                getActionIdentity(
                                    currentMountedActions[index],
                                ) !== action
                            )
                                operation.reopenedModals.delete(index)
                        }
                        if (stillMounted) {
                            operation.focus = false
                            operation.modal = true
                            this.setSortingDisabled(true)
                            const index = currentMountedActions.length - 1
                            if (
                                currentMountedActions.length < mounted.length &&
                                operation.reopenedModals?.has(index)
                            ) {
                                this.schedule(() => {
                                    if (!this.isCurrent(operation)) return
                                    const actions =
                                        component.canonical.mountedActions ?? []
                                    if (
                                        actions.length === index + 1 &&
                                        getActionIdentity(actions[index]) ===
                                            operation.reopenedModals.get(index)
                                    ) {
                                        this.$dispatch('open-modal', {
                                            id: `fi-${component.id}-action-${index}`,
                                        })
                                    }
                                })
                            }
                            return
                        }
                        if (
                            !rendersOwner(effects) ||
                            cancelling ||
                            isDescendantAction
                        ) {
                            this.resynchronize(operation)
                            return
                        }
                        this.schedule(() => this.reconcile(operation))
                    })
                    fail(() => {
                        if (!this.isCurrent(operation)) return
                        operation.focus = false
                        if (operation.modal && cancelling) {
                            operation.reopenedModals ??= new Map()
                            operation.reopenedModals.set(
                                mounted.length - 1,
                                getActionIdentity(mounted.at(-1)),
                            )
                            this.$dispatch('open-modal', {
                                id: `fi-${component.id}-action-${mounted.length - 1}`,
                            })
                        }
                        if (operation.modal) return
                        if (operation.direction === 'drag') {
                            this.resynchronize(operation)
                        } else {
                            this.finish()
                        }
                    })
                },
            )
            this.updateHandles()
        },

        owns(element) {
            return element.closest('[data-reorder-owner]') === this.$root
        },

        order() {
            return JSON.parse(this.$root.dataset.reorderItems)
        },

        reorder(event) {
            if (event.target !== this.$refs.reorderItems) return
            this.$wire.mountAction(
                reorderAction,
                { items: event.target.sortable.toArray() },
                { schemaComponent },
            )
        },

        begin(item, direction, control = null) {
            this.pending = {
                item,
                direction,
                control,
                focus:
                    !control?.hasAttribute('data-reorder-hidden') &&
                    document.activeElement === control,
                before: this.order(),
                action:
                    direction === 'drag'
                        ? reorderAction
                        : direction === 'up'
                          ? moveUpAction
                          : moveDownAction,
            }
            if (direction !== 'drag') this.setSortingDisabled(true)
        },

        relinquishFocus() {
            if (this.pending) this.pending.focus = false
        },

        isCurrent(operation) {
            return (
                !this.destroyed &&
                this.$root.isConnected &&
                this.pending === operation
            )
        },

        schedule(callback) {
            cancelAnimationFrame(this.frame)
            this.frame = requestAnimationFrame(callback)
        },

        resynchronize(operation) {
            this.setSortingDisabled(true)
            operation.synchronizing = true
            this.$wire.forceRender().catch(() => {
                // A later owner render can recover an unverified server order.
            })
        },

        reconcile(operation) {
            if (!this.isCurrent(operation)) return
            this.updateHandles()
            const after = this.order()
            const beforePeers = operation.before.filter((key) =>
                after.includes(key),
            )
            const afterPeers = after.filter((key) =>
                operation.before.includes(key),
            )
            const moved =
                after.includes(operation.item) &&
                beforePeers.indexOf(operation.item) !==
                    afterPeers.indexOf(operation.item)
            const description = [
                ...this.$root.querySelectorAll('[data-reorder-description]'),
            ].find(
                (element) =>
                    this.owns(element) &&
                    element.dataset.reorderDescription === operation.item,
            )

            if (moved && description) {
                this.$refs.reorderStatus.textContent = message.replace(
                    /:label|:position|:count/g,
                    (placeholder) =>
                        ({
                            ':label': description.dataset.reorderLabel,
                            ':position': String(
                                after.indexOf(operation.item) + 1,
                            ),
                            ':count': String(after.length),
                        })[placeholder],
                )
                if (
                    operation.focus &&
                    !operation.modal &&
                    [document.body, operation.control].includes(
                        document.activeElement,
                    )
                ) {
                    const item = [
                        ...this.$root.querySelectorAll('[x-sortable-item]'),
                    ].find(
                        (element) =>
                            this.owns(element) &&
                            element.parentElement === this.$refs.reorderItems &&
                            element.getAttribute('x-sortable-item') ===
                                operation.item,
                    )
                    const controls = [
                        ...(item?.querySelectorAll(
                            '[data-reorder-direction]',
                        ) ?? []),
                    ].filter(
                        (control) =>
                            this.owns(control) &&
                            !control.hasAttribute('data-reorder-hidden') &&
                            !control.disabled &&
                            control.getAttribute('aria-disabled') !== 'true' &&
                            this.isElementVisible(control),
                    )
                    const control =
                        controls.find(
                            (control) =>
                                control.dataset.reorderDirection ===
                                operation.direction,
                        ) ?? controls[0]
                    if (control) control.focus({ preventScroll: true })
                    else if (item) {
                        item.tabIndex = -1
                        if (
                            !item.hasAttribute('aria-label') &&
                            !item.hasAttribute('aria-labelledby')
                        ) {
                            item.setAttribute('aria-labelledby', description.id)
                        }
                        item.focus({ preventScroll: true })
                    }
                }
            }
            if (
                !after.includes(operation.item) &&
                operation.focus &&
                !operation.modal &&
                document.activeElement === document.body
            ) {
                this.$root.tabIndex = -1
                this.$root.focus({ preventScroll: true })
            }
            this.finish()
        },

        setSortingDisabled(disabled) {
            this.$refs.reorderItems?.sortable?.option('disabled', disabled)
        },

        isElementVisible(element) {
            const style = window.getComputedStyle(element)

            return (
                style.display !== 'none' &&
                style.visibility !== 'hidden' &&
                element.getClientRects().length > 0
            )
        },

        updateHandles() {
            if (hasCustomReorderAction) return
            for (const handle of this.$refs.reorderItems?.querySelectorAll(
                '[x-sortable-handle]',
            ) ?? []) {
                if (
                    !this.owns(handle) ||
                    handle.closest('[x-sortable]') !== this.$refs.reorderItems
                )
                    continue
                const item = handle.closest('[x-sortable-item]')
                const hasMoveControl = [
                    ...item.querySelectorAll('[data-reorder-direction]'),
                ].some(
                    (control) =>
                        this.owns(control) &&
                        !control.disabled &&
                        control.getAttribute('aria-disabled') !== 'true' &&
                        this.isElementVisible(control),
                )
                if (hasMoveControl) {
                    handle.tabIndex = -1
                    handle.setAttribute('aria-hidden', 'true')
                } else {
                    handle.removeAttribute('tabindex')
                    handle.removeAttribute('aria-hidden')
                }
            }
        },

        finish() {
            this.pending = null
            this.setSortingDisabled(false)
        },

        destroy() {
            this.destroyed = true
            this.pending = null
            cancelAnimationFrame(this.frame)
            this.removeCommitHook?.()
            this.listeners.forEach((remove) => remove())
        },
    }
}
