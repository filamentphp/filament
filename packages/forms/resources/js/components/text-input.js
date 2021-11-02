import IMask from 'imask'

export default (Alpine) => {
    Alpine.data('textInputFormComponent', ({
        getMaskOptionsUsing,
        state,
    }) => {
        return {
            mask: null,

            state,

            init: function () {
                if (! getMaskOptionsUsing) {
                    return
                }

                if (this.state) {
                    this.$el.value = this.state?.valueOf()
                }

                this.mask = IMask(this.$el, getMaskOptionsUsing(IMask)).on('accept', () => {
                    this.state = this.mask.unmaskedValue
                })

                this.$watch('state', () => {
                    this.mask.unmaskedValue = this.state?.valueOf()
                })
            }
        }
    })
}
