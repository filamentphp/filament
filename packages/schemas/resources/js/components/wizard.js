export default function wizardSchemaComponent({
    isSkippable,
    isStepPersistedInQueryString,
    key,
    startStep,
    stepQueryStringKey,
}) {
    return {
        step: null,

        init: function () {
            this.$watch('step', () => this.updateQueryString())

            this.step = this.getSteps().at(startStep - 1)

            this.autofocusFields()
        },

        requestNextStep: async function () {
            await this.$wire.callSchemaComponentMethod(key, 'nextStep', {
                currentStepIndex: this.getStepIndex(this.step),
            })
        },

        goToNextStep: function () {
            let nextStepIndex = this.getStepIndex(this.step) + 1

            if (nextStepIndex >= this.getSteps().length) {
                return
            }

            this.step = this.getSteps()[nextStepIndex]

            this.autofocusFields()
            this.scroll()
        },

        goToPreviousStep: function () {
            let previousStepIndex = this.getStepIndex(this.step) - 1

            if (previousStepIndex < 0) {
                return
            }

            this.step = this.getSteps()[previousStepIndex]

            this.autofocusFields()
            this.scroll()
        },

        scroll: function () {
            this.$nextTick(() => {
                this.$refs.header.children[
                    this.getStepIndex(this.step)
                ].scrollIntoView({ behavior: 'smooth', block: 'start' })
            })
        },

        autofocusFields: function () {
            this.$nextTick(() =>
                this.$refs[`step-${this.step}`]
                    .querySelector('[autofocus]')
                    ?.focus(),
            )
        },

        getStepIndex: function (step) {
            let index = this.getSteps().findIndex(
                (indexedStep) => indexedStep === step,
            )

            if (index === -1) {
                return 0
            }

            return index
        },

        getSteps: function () {
            return JSON.parse(this.$refs.stepsData.value)
        },

        isFirstStep: function () {
            return this.getStepIndex(this.step) <= 0
        },

        isLastStep: function () {
            return this.getStepIndex(this.step) + 1 >= this.getSteps().length
        },

        isStepAccessible: function (stepKey) {
            return (
                isSkippable ||
                this.getStepIndex(this.step) > this.getStepIndex(stepKey)
            )
        },

        updateQueryString: function () {
            if (!isStepPersistedInQueryString) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(stepQueryStringKey, this.step)

            history.pushState(null, document.title, url.toString())
        },
    }
}
