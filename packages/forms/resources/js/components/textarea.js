export default function textareaFormComponent({ initialHeight }) {
    return {
        height: initialHeight + 'rem',

        init: function () {
            this.render()
        },

        render: function () {
            if (this.$el.scrollHeight > 0) {
                this.$el.style.height = initialHeight + 'rem'
                this.height = this.$el.scrollHeight + 'px'
                this.$el.style.height = this.height
            }
        },

        styles: {
            [':style']() {
                return {
                    height: this.height,
                };
            },
        }
    }
}
