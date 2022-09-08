export default (Alpine) => {
    Alpine.data('textareaFormComponent', () => ({
        init: function () {
            this.$nextTick(() => {
                this.render()
            })
        },

        render: function () {
            if (this.$el.scrollHeight > 0) {
                this.$el.style.height = '150px'
                this.$el.style.height = this.$el.scrollHeight + 2 + 'px'
            }
        },
    }))
}
