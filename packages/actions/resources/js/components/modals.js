export default ({ livewireId }) => ({
    actionNestingIndex: null,

    shouldOverlayParentActions: false,

    closedActionNestingIndexes: [],

    focusTargetsByNestingIndex: {},

    boundSyncActionModals: null,

    boundOnModalClosed: null,

    boundFocusTarget: null,

    boundFocusTargetFinished: null,

    pendingFocusTarget: null,

    restoringFocusTarget: null,

    init() {
        this.boundFocusTarget = (event) => {
            if (event.detail.id !== livewireId) return

            this.pendingFocusTarget?.controller.abort()
            this.restoringFocusTarget?.controller.abort()

            const controller = new AbortController()
            this.pendingFocusTarget = {
                controller,
                create: event.detail.createFocusTarget,
                restore: event.detail.createFocusTarget(controller.signal),
            }
        }

        this.boundFocusTargetFinished = (event) => {
            if (
                event.detail.id !== livewireId ||
                this.pendingFocusTarget?.create !==
                    event.detail.createFocusTarget
            )
                return

            this.pendingFocusTarget.controller.abort()
            this.pendingFocusTarget = null
        }

        this.boundSyncActionModals = (event) => {
            if (event.detail.id !== livewireId) {
                return
            }

            this.syncActionModals(
                event.detail.newActionNestingIndex,
                event.detail.shouldOverlayParentActions ?? false,
            )
        }

        this.boundOnModalClosed = (event) => {
            const actionNestingIndex = this.getActionNestingIndexFromModalId(
                event.detail.id,
            )

            if (actionNestingIndex === null) {
                return
            }

            // Stacked mode and top modal return close immediately restore focus (close the modal without waiting for Livewire requests upon return)
            if (this.shouldOverlayParentActions || actionNestingIndex === 0) {
                this.restorePreviouslyFocusedElement(actionNestingIndex - 1)
            }

            this.closedActionNestingIndexes.push(actionNestingIndex)
        }

        window.addEventListener(
            'sync-action-modals',
            this.boundSyncActionModals,
        )

        window.addEventListener('modal-closed', this.boundOnModalClosed)
        window.addEventListener(
            'action-modal-focus-target',
            this.boundFocusTarget,
        )
        window.addEventListener(
            'action-modal-focus-target-finished',
            this.boundFocusTargetFinished,
        )
    },

    syncActionModals(
        newActionNestingIndex,
        shouldOverlayParentActions = false,
    ) {
        if (this.actionNestingIndex === newActionNestingIndex) {
            // https://github.com/filamentphp/filament/issues/16474
            this.actionNestingIndex !== null &&
                this.$nextTick(() => this.openModal())

            return
        }

        const isNestingIncrease =
            this.actionNestingIndex !== null &&
            newActionNestingIndex !== null &&
            newActionNestingIndex > this.actionNestingIndex

        const isNestingDecrease =
            this.actionNestingIndex !== null &&
            newActionNestingIndex !== null &&
            newActionNestingIndex < this.actionNestingIndex

        const isEnteringActionModalStack =
            this.actionNestingIndex === null && newActionNestingIndex !== null

        if (isNestingIncrease || isEnteringActionModalStack) {
            this.restoringFocusTarget?.controller.abort()
            this.rememberPreviouslyFocusedElement()
        }

        if (
            this.actionNestingIndex !== null &&
            !(shouldOverlayParentActions && isNestingIncrease)
        ) {
            this.closeModal()
        }

        this.actionNestingIndex = newActionNestingIndex

        if (this.actionNestingIndex === null) {
            this.restorePreviouslyFocusedElement(-1)
            this.closedActionNestingIndexes = []
            this.focusTargetsByNestingIndex = {}
            this.shouldOverlayParentActions = false

            return
        }

        this.shouldOverlayParentActions = shouldOverlayParentActions

        this.closedActionNestingIndexes =
            this.closedActionNestingIndexes.filter(
                (closedActionNestingIndex) =>
                    closedActionNestingIndex <= this.actionNestingIndex,
            )

        if (this.closedActionNestingIndexes.includes(this.actionNestingIndex)) {
            return
        }

        if (
            !this.$el.querySelector(
                `#${this.generateModalId(newActionNestingIndex)}`,
            )
        ) {
            this.$nextTick(() => {
                this.openModal()

                if (isNestingDecrease) {
                    this.restorePreviouslyFocusedElement()
                }
            })

            return
        }

        this.openModal()
        if (isNestingDecrease) {
            this.restorePreviouslyFocusedElement()
        }
    },

    rememberPreviouslyFocusedElement() {
        if (this.pendingFocusTarget) {
            this.focusTargetsByNestingIndex[this.actionNestingIndex ?? -1] =
                this.pendingFocusTarget
            this.pendingFocusTarget = null
            return
        }

        const focused = this.$focus.focused()

        if (!focused) {
            return
        }

        if (this.actionNestingIndex === null) {
            this.focusTargetsByNestingIndex[-1] = focused
            return
        }

        const modal = this.$el.querySelector(
            `#${this.generateModalId(this.actionNestingIndex)}`,
        )

        if (!modal?.contains(focused)) {
            return
        }

        this.focusTargetsByNestingIndex[this.actionNestingIndex] = focused
    },

    restorePreviouslyFocusedElement(
        actionNestingIndex = this.actionNestingIndex,
    ) {
        const previouslyFocusedElement =
            this.focusTargetsByNestingIndex[actionNestingIndex]

        for (const focusTargetNestingIndex in this.focusTargetsByNestingIndex) {
            if (Number(focusTargetNestingIndex) >= actionNestingIndex) {
                const target =
                    this.focusTargetsByNestingIndex[focusTargetNestingIndex]
                if (target !== previouslyFocusedElement)
                    target.controller?.abort()
                delete this.focusTargetsByNestingIndex[focusTargetNestingIndex]
            }
        }

        if (this.restoringFocusTarget?.nestingIndex > actionNestingIndex) {
            this.restoringFocusTarget.controller.abort()
            this.restoringFocusTarget = null
        }

        if (!previouslyFocusedElement) {
            return
        }

        this.restoringFocusTarget?.controller.abort()
        this.restoringFocusTarget = previouslyFocusedElement.restore
            ? previouslyFocusedElement
            : null

        if (this.restoringFocusTarget) {
            this.restoringFocusTarget.nestingIndex = actionNestingIndex
        }

        requestAnimationFrame(() =>
            requestAnimationFrame(() =>
                this.$nextTick(async () => {
                    if (!previouslyFocusedElement.restore) {
                        previouslyFocusedElement.focus({ preventScroll: true })
                        return
                    }

                    const { controller } = previouslyFocusedElement
                    if (controller.signal.aborted) return

                    try {
                        await previouslyFocusedElement.restore()
                    } finally {
                        controller.abort()
                        if (
                            this.restoringFocusTarget ===
                            previouslyFocusedElement
                        ) {
                            this.restoringFocusTarget = null
                        }
                    }
                }),
            ),
        )
    },

    generateModalId(actionNestingIndex) {
        // HTML IDs must start with a letter, so if the Livewire component ID starts
        // with a number, we need to make sure it does not fail by prepending `fi-`.
        return `fi-${livewireId}-action-` + actionNestingIndex
    },

    getActionNestingIndexFromModalId(id) {
        const prefix = `fi-${livewireId}-action-`

        if (!id?.startsWith(prefix)) {
            return null
        }

        const actionNestingIndex = Number(id.slice(prefix.length))

        return Number.isInteger(actionNestingIndex) ? actionNestingIndex : null
    },

    openModal() {
        const id = this.generateModalId(this.actionNestingIndex)

        document.dispatchEvent(
            new CustomEvent('open-modal', {
                bubbles: true,
                composed: true,
                detail: { id },
            }),
        )
    },

    closeModal() {
        const id = this.generateModalId(this.actionNestingIndex)

        document.dispatchEvent(
            new CustomEvent('close-modal-quietly', {
                bubbles: true,
                composed: true,
                detail: { id },
            }),
        )
    },

    destroy() {
        this.pendingFocusTarget?.controller.abort()
        this.restoringFocusTarget?.controller.abort()
        Object.values(this.focusTargetsByNestingIndex).forEach((target) =>
            target.controller?.abort(),
        )
        window.removeEventListener(
            'action-modal-focus-target',
            this.boundFocusTarget,
        )
        window.removeEventListener(
            'action-modal-focus-target-finished',
            this.boundFocusTargetFinished,
        )

        if (this.boundSyncActionModals) {
            window.removeEventListener(
                'sync-action-modals',
                this.boundSyncActionModals,
            )

            this.boundSyncActionModals = null
        }

        if (this.boundOnModalClosed) {
            window.removeEventListener('modal-closed', this.boundOnModalClosed)

            this.boundOnModalClosed = null
        }
    },
})
