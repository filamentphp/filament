export default function textareaFormComponent({ initialHeight }) {
    return {
        init: function () {
            this.render()
        },

        render: function () {
            if (this.$el.scrollHeight > 0) {
                this.$el.style.height = initialHeight + 'rem'
                this.$el.style.height = this.$el.scrollHeight + 'px'
            }
        },

        input: {
            ['@intersect.once']() {
                this.render()
            },
            ['@input']() {
                this.render()
            },
            ['@resize.window']() {
                this.render()
            },
            ['@form-validation-error.window']() {
                this.$nextTick(() => {
                    this.render()
                })
            },
        },
    }
}
