export default ({ livewireId }) => ({
    actionNestingIndex: null,

    shouldOverlayParentActions: false,

    closedActionNestingIndexes: [],

    focusTargetsByNestingIndex: {},

    boundSyncActionModals: null,

    boundOnModalClosed: null,

    init() {
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
    },

    destroy() {
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

        if (!previouslyFocusedElement) {
            return
        }

        for (const focusTargetNestingIndex in this.focusTargetsByNestingIndex) {
            if (Number(focusTargetNestingIndex) >= actionNestingIndex) {
                delete this.focusTargetsByNestingIndex[focusTargetNestingIndex]
            }
        }

        requestAnimationFrame(() =>
            requestAnimationFrame(() =>
                this.$nextTick(() => {
                    previouslyFocusedElement.focus({ preventScroll: false })
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
})
