export default function wizardSchemaComponent({
    isSkippable,
    isStepPersistedInQueryString,
    key,
    livewireId,
    schemaKey,
    startStep,
    stepQueryStringKey,
}) {
    return {
        boundResetHandler: null,
        step: null,

        init() {
            const steps = this.getSteps()
            const queryStringStep = new URLSearchParams(
                window.location.search,
            ).get(stepQueryStringKey)

            this.step =
                isStepPersistedInQueryString && steps.includes(queryStringStep)
                    ? queryStringStep
                    : steps.at(startStep - 1)

            this.$watch('step', () => {
                this.updateQueryString()
                this.autofocusFields()
            })

            this.autofocusFields(true)

            this.boundResetHandler = (event) => {
                if (
                    event.detail.livewireId !== livewireId ||
                    event.detail.schemaKey !== schemaKey ||
                    isStepPersistedInQueryString
                ) {
                    return
                }

                this.$nextTick(() => {
                    this.step = this.getSteps().at(startStep - 1) ?? this.step
                })
            }

            window.addEventListener(
                'reset-schema-component-state',
                this.boundResetHandler,
            )
        },

        async requestNextStep() {
            await this.$wire.callSchemaComponentMethod(key, 'nextStep', {
                currentStepIndex: this.getStepIndex(this.step),
                currentStepKey: this.step,
            })
        },

        goToNextStep(nextStep = null, currentStep = null) {
            if (currentStep !== null && this.step !== currentStep) {
                return
            }

            if (nextStep === null) {
                const currentStepIndex = this.getStepIndex(this.step)

                if (currentStepIndex === -1) {
                    return
                }

                nextStep = this.getSteps()[currentStepIndex + 1]
            }

            if (!this.getSteps().includes(nextStep)) {
                return
            }

            this.step = nextStep

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
            return this.getSteps().findIndex(
                (indexedStep) => indexedStep === step,
            )
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
                this.getStepIndex(stepKey) !== -1 &&
                (isSkippable ||
                    this.getStepIndex(this.step) > this.getStepIndex(stepKey))
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

        destroy() {
            if (this.boundResetHandler) {
                window.removeEventListener(
                    'reset-schema-component-state',
                    this.boundResetHandler,
                )
            }
        },
    }
}
