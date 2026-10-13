import normalizeSearchString from '../../../../support/resources/js/utilities/normalize-search-string.js'

export default function checkboxListFormComponent({ livewireId }) {
    return {
        areAllCheckboxesChecked: false,

        checkboxListOptions: [],

        search: '',

        unsubscribeLivewireHook: null,

        visibleCheckboxListOptions: [],

        init() {
            this.checkboxListOptions = Array.from(
                this.$root.querySelectorAll('.fi-fo-checkbox-list-option'),
            )

            this.updateVisibleCheckboxListOptions()

            this.$nextTick(() => {
                this.checkIfAllCheckboxesAreChecked()
            })

            this.unsubscribeLivewireHook = Livewire.interceptMessage(
                ({ message, onSuccess }) => {
                    if (message.component.id !== livewireId) {
                        return
                    }

                    onSuccess(({ onMorphed }) =>
                        onMorphed(() => {
                            this.$nextTick(() => {
                                if (!this.$root.isConnected) {
                                    return
                                }

                                this.checkboxListOptions = Array.from(
                                    this.$root.querySelectorAll(
                                        '.fi-fo-checkbox-list-option',
                                    ),
                                )

                                this.updateVisibleCheckboxListOptions()

                                this.checkIfAllCheckboxesAreChecked()
                            })
                        }),
                    )
                },
            )

            this.$watch('search', () => {
                this.updateVisibleCheckboxListOptions()
                this.checkIfAllCheckboxesAreChecked()
            })
        },

        checkIfAllCheckboxesAreChecked() {
            this.areAllCheckboxesChecked =
                this.visibleCheckboxListOptions.length ===
                this.visibleCheckboxListOptions.filter((checkboxLabel) =>
                    checkboxLabel.querySelector(
                        'input[type=checkbox]:checked, input[type=checkbox]:disabled',
                    ),
                ).length
        },

        toggleAllCheckboxes() {
            this.checkIfAllCheckboxesAreChecked()

            const inverseAreAllCheckboxesChecked = !this.areAllCheckboxesChecked

            this.visibleCheckboxListOptions.forEach((checkboxLabel) => {
                const checkbox = checkboxLabel.querySelector(
                    'input[type=checkbox]',
                )

                if (checkbox.disabled) {
                    return
                }

                if (checkbox.checked === inverseAreAllCheckboxesChecked) {
                    return
                }

                checkbox.checked = inverseAreAllCheckboxesChecked
                checkbox.dispatchEvent(new Event('change'))
            })

            this.areAllCheckboxesChecked = inverseAreAllCheckboxesChecked
        },

        updateVisibleCheckboxListOptions() {
            this.visibleCheckboxListOptions = this.checkboxListOptions.filter(
                (checkboxListItem) => this.matchesSearch(checkboxListItem),
            )
        },

        matchesSearch(checkboxListItem) {
            if (['', null, undefined].includes(this.search)) {
                return true
            }

            const search = normalizeSearchString(this.search)

            return (
                normalizeSearchString(
                    checkboxListItem.querySelector(
                        '.fi-fo-checkbox-list-option-label',
                    )?.innerText ?? '',
                ).includes(search) ||
                normalizeSearchString(
                    checkboxListItem.querySelector(
                        '.fi-fo-checkbox-list-option-description',
                    )?.innerText ?? '',
                ).includes(search)
            )
        },

        destroy() {
            this.unsubscribeLivewireHook?.()
        },
    }
}
