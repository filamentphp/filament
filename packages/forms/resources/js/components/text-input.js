import IMask from 'imask'

export default function textInputFormComponent({ getMaskOptionsUsing, state }) {
    return {
        isStateBeingUpdated: false,

        mask: null,

        state,

        init: function () {
            if (!getMaskOptionsUsing) {
                return
            }

            if (this.state) {
                this.$el.value = this.state?.valueOf()
            }

            this.mask = IMask(this.$el, getMaskOptionsUsing(IMask)).on(
                'accept',
                () => {
                    this.isStateBeingUpdated = true

                    this.state = this.mask.unmaskedValue

                    this.$nextTick(() => (this.isStateBeingUpdated = false))
                },
            )

            this.$watch('state', () => {
                if (this.isStateBeingUpdated) {
                    return
                }

                this.mask.unmaskedValue = this.state?.valueOf() ?? ''
            })
        },
    }
}
