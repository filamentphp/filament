export default function checkboxListFormComponent({ livewireId }) {
    return {
        areAllCheckboxesChecked: false,

        checkboxListOptions: [],

        search: '',

        visibleCheckboxListOptions: [],

        init: function () {
            this.checkboxListOptions = Array.from(
                this.$root.querySelectorAll(
                    '.fi-fo-checkbox-list-option-label',
                ),
            )

            this.updateVisibleCheckboxListOptions()

            this.$nextTick(() => {
                this.checkIfAllCheckboxesAreChecked()
            })

            Livewire.hook(
                'commit',
                ({ component, commit, succeed, fail, respond }) => {
                    succeed(({ snapshot, effect }) => {
                        this.$nextTick(() => {
                            if (component.id !== livewireId) {
                                return
                            }

                            this.checkboxListOptions = Array.from(
                                this.$root.querySelectorAll(
                                    '.fi-fo-checkbox-list-option-label',
                                ),
                            )

                            this.updateVisibleCheckboxListOptions()

                            this.checkIfAllCheckboxesAreChecked()
                        })
                    })
                },
            )

            this.$watch('search', () => {
                this.updateVisibleCheckboxListOptions()
                this.checkIfAllCheckboxesAreChecked()
            })
        },

        checkIfAllCheckboxesAreChecked: function () {
            this.areAllCheckboxesChecked =
                this.visibleCheckboxListOptions.length ===
                this.visibleCheckboxListOptions.filter((checkboxLabel) =>
                    checkboxLabel.querySelector('input[type=checkbox]:checked'),
                ).length
        },

        toggleAllCheckboxes: function () {
            this.visibleCheckboxListOptions.forEach((checkboxLabel) => {
                const checkbox = checkboxLabel.querySelector(
                    'input[type=checkbox]',
                )

                if (checkbox.disabled) {
                    return
                }

                checkbox.checked = !this.areAllCheckboxesChecked
                checkbox.dispatchEvent(new Event('change'))
            })

            this.areAllCheckboxesChecked = !this.areAllCheckboxesChecked
        },

        updateVisibleCheckboxListOptions: function () {
            this.visibleCheckboxListOptions = this.checkboxListOptions.filter(
                (checkboxListItem) => {
                    if (
                        checkboxListItem
                            .querySelector('.fi-fo-checkbox-list-option-label')
                            ?.innerText.toLowerCase()
                            .includes(this.search.toLowerCase())
                    ) {
                        return true
                    }

                    return checkboxListItem
                        .querySelector(
                            '.fi-fo-checkbox-list-option-description',
                        )
                        ?.innerText.toLowerCase()
                        .includes(this.search.toLowerCase())
                },
            )
        },
    }
}
