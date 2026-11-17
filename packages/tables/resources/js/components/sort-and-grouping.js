function compositeSelector({
    value,
    livewireProperty,
    primary,
    shouldClearSelectionOnEmptyValue,
    shouldResetDirectionOnFirstSelection,
}) {
    return {
        [value]: this.$wire.$entangle(livewireProperty, true),

        [primary]: null,

        direction: null,

        init() {
            this.splitValue()

            this.$watch(value, () => {
                if (!this[value]) {
                    if (shouldClearSelectionOnEmptyValue) {
                        this[primary] = null
                        this.direction = null
                    }

                    return
                }

                this.splitValue()
            })

            this.$watch('direction', () => this.joinValue())

            this.$watch(primary, (newPrimary, oldPrimary) => {
                if (!newPrimary) {
                    this.direction = null
                    this.joinValue()

                    return
                }

                if (oldPrimary) {
                    this.joinValue()

                    return
                }

                if (shouldResetDirectionOnFirstSelection) {
                    this.direction = 'asc'
                } else {
                    this.direction ??= 'asc'
                }

                this.joinValue()
            })
        },

        splitValue() {
            if (!this[value]) {
                return
            }

            ;[this[primary], this.direction] = this[value].split(':')
            this.direction ??= 'asc'
        },

        joinValue() {
            this[value] = this[primary]
                ? `${this[primary]}:${this.direction}`
                : null
        },
    }
}

export function sort() {
    return compositeSelector.call(this, {
        value: 'sort',
        livewireProperty: 'tableSort',
        primary: 'column',
        shouldClearSelectionOnEmptyValue: false,
        shouldResetDirectionOnFirstSelection: true,
    })
}

export function grouping() {
    return compositeSelector.call(this, {
        value: 'grouping',
        livewireProperty: 'tableGrouping',
        primary: 'group',
        shouldClearSelectionOnEmptyValue: true,
        shouldResetDirectionOnFirstSelection: false,
    })
}
