export default (Alpine) => {
    Alpine.data('textareaFormComponent', () => ({
        init() {
            this.resize()
        },
        resize() {
            this.$root.style.height = 'auto'
            this.$root.style.height = this.$root.scrollHeight + 'px'
        }
    }))
}
