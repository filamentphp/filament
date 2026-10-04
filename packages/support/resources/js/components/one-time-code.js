export default ({ shouldSubmitOnCompletion = false } = {}) => ({
    inputs: [],

    lastSubmittedValue: null,

    pendingSubmissionValue: null,

    state: null,

    init() {
        this.inputs = Array.from(
            this.$root.querySelectorAll('.fi-one-time-code-input-digit'),
        )

        this.distribute(this.state)

        this.$watch('state', (state) => {
            if (this.read() === (state ?? '')) {
                return
            }

            this.distribute(state)
        })

        this.inputs.forEach((input, index) => {
            input.addEventListener('input', (event) => {
                event.stopPropagation()

                let value = event.target.value

                // A value longer than one character has been autofilled or pasted, so it is
                // spread across every input instead of being treated as a single digit.
                if (value.length > 1) {
                    this.distribute(value)
                    this.commit()
                    this.focusInput(this.getFirstEmptyInputIndex())
                    this.submitIfComplete()

                    return
                }

                if (value === '') {
                    this.commit()
                    this.submitIfComplete()

                    return
                }

                if (/\D/.test(value)) {
                    event.target.value = ''
                    this.submitIfComplete()

                    return
                }

                this.commit()
                this.focusInput(index + 1)
                this.submitIfComplete()
            })

            input.addEventListener('keydown', (event) => {
                if (
                    ['Backspace', 'Delete'].includes(event.key) &&
                    event.target.value === ''
                ) {
                    this.focusInput(index - 1)

                    return
                }

                if (event.key === 'ArrowLeft') {
                    this.focusInput(index - 1)
                    event.preventDefault()
                }

                if (event.key === 'ArrowRight') {
                    this.focusInput(index + 1)
                    event.preventDefault()
                }
            })

            // Select the existing character on focus, so that typing replaces it.
            input.addEventListener('focus', (event) =>
                event.target.setSelectionRange(0, 1),
            )

            // Prevent focusing an empty input ahead of the next one to be filled.
            input.addEventListener('pointerdown', (event) => {
                let firstEmptyInputIndex = Math.min(
                    index,
                    this.getFirstEmptyInputIndex(),
                )

                if (firstEmptyInputIndex !== index) {
                    event.preventDefault()
                    this.focusInput(firstEmptyInputIndex)
                }
            })
        })
    },

    distribute(value) {
        let digits = (value ?? '')
            .toString()
            .replace(/\D/g, '')
            .slice(0, this.inputs.length)

        this.inputs.forEach((input, index) => {
            input.value = digits[index] ?? ''
        })

        if (digits.length < this.inputs.length) {
            this.lastSubmittedValue = null
            this.pendingSubmissionValue = null
        }
    },

    read() {
        return this.inputs.map((input) => input.value).join('')
    },

    commit() {
        let value = this.read()

        this.state = value === '' ? null : value
    },

    submitIfComplete() {
        if (!shouldSubmitOnCompletion) {
            return
        }

        let value = this.read()

        if (value.length < this.inputs.length) {
            this.lastSubmittedValue = null
            this.pendingSubmissionValue = null

            return
        }

        if (
            value === this.lastSubmittedValue ||
            value === this.pendingSubmissionValue
        ) {
            return
        }

        this.pendingSubmissionValue = value

        // Wait for the committed state to sync with Livewire before submitting.
        this.$nextTick(() => {
            if (this.pendingSubmissionValue !== value) {
                return
            }

            this.pendingSubmissionValue = null

            if (this.read() !== value) {
                return
            }

            let form = this.$root.closest('form')

            if (!form) {
                return
            }

            this.lastSubmittedValue = value
            form.requestSubmit()
        })
    },

    getFirstEmptyInputIndex() {
        let index = this.inputs.findIndex((input) => input.value === '')

        return index === -1 ? this.inputs.length - 1 : index
    },

    focusInput(index) {
        let input =
            this.inputs[Math.max(0, Math.min(index, this.inputs.length - 1))]

        if (!input) {
            return
        }

        if (document.activeElement === input) {
            input.setSelectionRange(0, 1)

            return
        }

        input.focus()
    },
})
