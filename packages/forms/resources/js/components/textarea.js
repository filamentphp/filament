export default function textareaFormComponent({ initialHeight }) {
    return {
        init: function () {
            if (this.$el.scrollHeight > 0) {
                this.$el.style.minHeight = initialHeight + 'rem'
                this.$el.style.minHeight = this.$el.scrollHeight + 'px'
            }
        },

        render: function () {
            //
        },
    }
}
