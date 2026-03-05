export default function wizardSchemaComponent({
    isSkippable,
    isStepPersistedInQueryString,
    key,
    startStep,
    stepQueryStringKey,
}) {
    return {
        step: null,

        init() {
            this.step = this.getSteps().at(startStep - 1)

            this.$watch('step', () => {
                this.updateQueryString()
                this.autofocusFields()
            })

            this.autofocusFields(true)
        },

        async requestNextStep() {
            await this.$wire.callSchemaComponentMethod(key, 'nextStep', {
                currentStepIndex: this.getStepIndex(this.step),
            })
        },

        goToNextStep() {
            let nextStepIndex = this.getStepIndex(this.step) + 1

            if (nextStepIndex >= this.getSteps().length) {
                return
            }

            this.step = this.getSteps()[nextStepIndex]

            this.scroll()
        },

        goToPreviousStep() {
            let previousStepIndex = this.getStepIndex(this.step) - 1

            if (previousStepIndex < 0) {
                return
            }

            this.step = this.getSteps()[previousStepIndex]

            this.scroll()
        },

        goToStep(stepKey) {
            const stepIndex = this.getStepIndex(stepKey)

            if (stepIndex <= -1) {
                return
            }

            if (!isSkippable && stepIndex > this.getStepIndex(this.step)) {
                return
            }

            this.step = stepKey

            this.scroll()
        },

        scroll() {
            this.$nextTick(() => {
                this.$refs.header?.children[
                    this.getStepIndex(this.step)
                ].scrollIntoView({ behavior: 'smooth', block: 'start' })
            })
        },

        autofocusFields(respectCurrentFocus = false) {
            this.$nextTick(() => {
                if (
                    respectCurrentFocus &&
                    document.activeElement &&
                    document.activeElement !== document.body &&
                    this.$el.compareDocumentPosition(document.activeElement) &
                        Node.DOCUMENT_POSITION_PRECEDING
                ) {
                    return
                }

                const fields =
                    this.$refs[`step-${this.step}`]?.querySelectorAll(
                        '[autofocus]',
                    ) ?? []

                for (const field of fields) {
                    field.focus()

                    if (document.activeElement === field) {
                        break
                    }
                }
            })
        },

        getStepIndex(step) {
            let index = this.getSteps().findIndex(
                (indexedStep) => indexedStep === step,
            )

            if (index === -1) {
                return 0
            }

            return index
        },

        getSteps() {
            return JSON.parse(this.$refs.stepsData.value)
        },

        isFirstStep() {
            return this.getStepIndex(this.step) <= 0
        },

        isLastStep() {
            return this.getStepIndex(this.step) + 1 >= this.getSteps().length
        },

        isStepAccessible(stepKey) {
            return (
                isSkippable ||
                this.getStepIndex(this.step) > this.getStepIndex(stepKey)
            )
        },

        updateQueryString() {
            if (!isStepPersistedInQueryString) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(stepQueryStringKey, this.step)

            history.replaceState(null, document.title, url.toString())
        },
    }
}
