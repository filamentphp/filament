export default function textareaFormComponent({ initialHeight }) {
    return {
        render: function () {
            if (this.$el.scrollHeight > 0) {
                this.$el.style.height = initialHeight + 'rem'
                this.$el.style.height = this.$el.scrollHeight + 'px'
            }
        },
    }
}
