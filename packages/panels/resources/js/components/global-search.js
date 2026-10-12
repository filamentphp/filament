export default ({ loadingMessage, failureMessage }) => ({
    isOpen: false,
    status: '',
    query: '',
    revision: 0,
    resultsRevision: 0,
    isDismissed: false,
    isSearching: false,
    isDestroyed: false,
    isRestoringFocus: false,
    focusWasInResults: false,
    loadingTimeout: null,
    failureTimeout: null,
    hookCleanups: [],

    init() {
        this.query = this.normalize(this.$wire.search)

        const componentInstance = this.$wire.__instance
        let responseRevision = this.revision

        this.hookCleanups = [
            Livewire.hook('morph', ({ component }) => {
                if (this.isDestroyed || component !== componentInstance) return

                this.focusWasInResults = this.$refs.results.contains(
                    document.activeElement,
                )
            }),
            Livewire.hook('morphed', ({ component }) => {
                if (this.isDestroyed || component !== componentInstance) return

                // A surviving DOM node may now represent a different result.
                if (
                    this.focusWasInResults &&
                    (document.activeElement === document.body ||
                        this.$refs.results.contains(document.activeElement))
                ) {
                    this.restoreFocus()
                }

                this.focusWasInResults = false
                this.resultsRevision = responseRevision
                this.synchronizeResults()
            }),
            Livewire.hook('commit', ({ component, commit, succeed, fail }) => {
                if (this.isDestroyed || component !== componentInstance) return

                const snapshot = JSON.parse(commit.snapshot).data
                const revision = this.revision
                const query = this.normalize(
                    commit.updates.search ?? snapshot.search,
                )

                succeed(({ effects }) => {
                    if (this.isDestroyed || !effects.html) return

                    responseRevision = revision
                })

                if (query !== this.query || !query || this.isDismissed) {
                    return
                }

                this.startLoading(revision)
                fail(() => this.searchFailed(revision))
            }),
        ]

        this.$nextTick(() => {
            if (!this.isDestroyed) this.synchronizeResults()
        })
    },

    normalize(value) {
        // Match PHP's `trim()` without changing internal whitespace or `0`.
        return String(value ?? '').replace(
            /^[ \t\n\r\0\v]+|[ \t\n\r\0\v]+$/g,
            '',
        )
    },

    inputChanged(value) {
        this.query = this.normalize(value)
        this.revision++
        this.isDismissed = false
        this.isSearching = !!this.query
        this.isOpen = false
        this.status = ''
        this.clearTimeouts()

        if (this.query) this.startFailureTimeout(this.revision)
    },

    hasCurrentResults() {
        return (
            !!this.query &&
            this.$refs.results.dataset.query === this.query &&
            this.resultsRevision === this.revision
        )
    },

    synchronizeResults() {
        if (!this.hasCurrentResults()) {
            this.isOpen = false

            return
        }

        this.clearTimeouts()
        this.isSearching = false
        this.isOpen =
            !this.isDismissed &&
            this.$refs.results.dataset.hasResults === 'true'
        this.status = this.isOpen ? this.$refs.results.dataset.message : ''
    },

    reopen() {
        if (this.isRestoringFocus) return

        this.isDismissed = false
        this.synchronizeResults()

        if (this.isSearching) this.startLoading(this.revision)
    },

    dismiss() {
        this.isDismissed = true
        this.isOpen = false
        this.status = ''
        this.clearTimeouts()
    },

    restoreFocus() {
        this.isRestoringFocus = true
        this.$refs.input.focus({ preventScroll: true })
        this.isRestoringFocus = false
    },

    focusLeft(event) {
        if (this.$el.contains(event.relatedTarget)) return

        this.$nextTick(() => {
            if (this.isDestroyed || this.$el.contains(document.activeElement)) {
                return
            }

            this.dismiss()
        })
    },

    keydown(event) {
        if (
            event.defaultPrevented ||
            event.isComposing ||
            event.altKey ||
            event.ctrlKey ||
            event.metaKey ||
            event.shiftKey
        ) {
            return
        }

        const target = event.target.closest(
            '.fi-global-search-result-link, a[data-global-search-action], button[data-global-search-action]',
        )

        if (event.target !== this.$refs.input && !target) return

        if (event.key === 'Escape') {
            if (!this.query) return

            event.preventDefault()
            event.stopPropagation()
            const shouldRestoreFocus = this.$refs.results.contains(event.target)
            this.dismiss()

            if (shouldRestoreFocus) this.restoreFocus()

            return
        }

        if (!['ArrowDown', 'ArrowUp'].includes(event.key)) return

        if (event.target === this.$refs.input) {
            if (event.key !== 'ArrowDown' || !this.query) return

            event.preventDefault()
            this.reopen()

            if (this.isOpen) {
                this.$nextTick(() => {
                    if (
                        this.isDestroyed ||
                        !this.isOpen ||
                        document.activeElement !== this.$refs.input
                    ) {
                        return
                    }

                    this.targets()[0]?.focus()
                })
            }

            return
        }

        const targets = this.targets()
        const index = targets.indexOf(
            target
                .closest('.fi-global-search-result')
                .querySelector('.fi-global-search-result-link'),
        )

        if (index === -1 || !this.isOpen) return

        event.preventDefault()
        event.stopPropagation()
        targets[
            (index + (event.key === 'ArrowDown' ? 1 : -1) + targets.length) %
                targets.length
        ].focus()
    },

    targets() {
        return [
            ...this.$refs.results.querySelectorAll(
                '.fi-global-search-result-link',
            ),
        ].filter(
            (element) =>
                !element.disabled &&
                element.getAttribute('aria-disabled') !== 'true' &&
                element.tabIndex >= 0 &&
                element.getClientRects().length,
        )
    },

    linkActivated(event) {
        const link = event.target.closest('a[href]')

        if (
            !link ||
            !link.matches(
                '.fi-global-search-result-link, a[data-global-search-action]',
            ) ||
            event.defaultPrevented ||
            event.ctrlKey ||
            event.metaKey ||
            event.shiftKey ||
            event.altKey ||
            link.target === '_blank' ||
            link.hasAttribute('wire:navigate')
        ) {
            return
        }

        this.dismiss()
    },

    navigationStarted(event) {
        queueMicrotask(() => {
            if (!this.isDestroyed && !event.defaultPrevented) this.dismiss()
        })
    },

    navigationCompleted() {
        this.inputChanged('')
        this.$wire.$set('search', '', false)
        this.$refs.input.value = ''
        this.dismiss()
    },

    startLoading(revision) {
        this.clearTimeouts()
        this.loadingTimeout = setTimeout(() => {
            if (this.isDestroyed || this.isDismissed) return

            this.status = loadingMessage
        }, 350)

        this.startFailureTimeout(revision)
    },

    startFailureTimeout(revision) {
        // A pending request can prevent the next query's `commit` from starting.
        this.failureTimeout = setTimeout(() => {
            this.searchFailed(revision)
        }, 30000)
    },

    searchFailed(revision) {
        if (
            this.isDestroyed ||
            this.isDismissed ||
            revision !== this.revision
        ) {
            return
        }

        this.clearTimeouts()
        this.isSearching = false
        this.isOpen = false
        this.status = failureMessage
    },

    clearTimeouts() {
        clearTimeout(this.loadingTimeout)
        clearTimeout(this.failureTimeout)
    },

    destroy() {
        this.isDestroyed = true
        this.clearTimeouts()
        this.hookCleanups.forEach((cleanup) => cleanup())
    },
})
