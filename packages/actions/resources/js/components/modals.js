export default ({ livewireId }) => ({
    actionNestingIndex: null,

    shouldOverlayParentActions: false,

    closedActionNestingIndexes: [],

    previouslyFocusedElementsByActionNestingIndex: {},

    init() {
        window.addEventListener('sync-action-modals', (event) => {
            if (event.detail.id !== livewireId) {
                return
            }

            this.syncActionModals(
                event.detail.newActionNestingIndex,
                event.detail.shouldOverlayParentActions ?? false,
            )
        })

        window.addEventListener('modal-closed', (event) => {
            const actionNestingIndex = this.getActionNestingIndexFromModalId(
                event.detail.id,
            )

            if (actionNestingIndex === null) {
                return
            }

            if (this.shouldOverlayParentActions) {
                this.$nextTick(() =>
                    this.restorePreviouslyFocusedElement(
                        actionNestingIndex - 1,
                    ),
                )
            }

            this.closedActionNestingIndexes.push(actionNestingIndex)
        })
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

        const shouldRestorePreviouslyFocusedElement =
            this.actionNestingIndex !== null &&
            newActionNestingIndex !== null &&
            newActionNestingIndex < this.actionNestingIndex

        if (isNestingIncrease) {
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
            this.closedActionNestingIndexes = []
            this.previouslyFocusedElementsByActionNestingIndex = {}
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

                if (shouldRestorePreviouslyFocusedElement) {
                    this.restorePreviouslyFocusedElement()
                }
            })

            return
        }

        this.openModal()
        if (shouldRestorePreviouslyFocusedElement) {
            this.restorePreviouslyFocusedElement()
        }
    },

    rememberPreviouslyFocusedElement() {
        const modal = this.$el.querySelector(
            `#${this.generateModalId(this.actionNestingIndex)}`,
        )

        const focused = this.$focus.focused()

        if (!modal?.contains(focused)) {
            return
        }

        this.previouslyFocusedElementsByActionNestingIndex[
            this.actionNestingIndex
        ] = focused
    },

    restorePreviouslyFocusedElement(
        actionNestingIndex = this.actionNestingIndex,
    ) {
        const previouslyFocusedElement =
            this.previouslyFocusedElementsByActionNestingIndex[
                actionNestingIndex
            ]

        if (!previouslyFocusedElement) {
            return
        }

        delete this.previouslyFocusedElementsByActionNestingIndex[
            actionNestingIndex
        ]

        requestAnimationFrame(() =>
            requestAnimationFrame(() =>
                this.$focus.focus(previouslyFocusedElement),
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
