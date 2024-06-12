export default function textareaFormComponent({ initialHeight }) {
    return {
        height: initialHeight + 'rem',

        init: function () {
            this.setInitialHeight();
            this.addResizeListener();
        },

        setInitialHeight: function () {
            if (this.$el.scrollHeight > 0) {
                this.$el.style.height = this.height;
            }
        },

        onResize: function () {
            if (this.$el.scrollHeight > 0) {
                // Only set the height if it differs from the current height
                const newHeight = this.$el.scrollHeight + 'px';
                
                if (this.height !== newHeight) {
                    this.height = newHeight;
                    this.$el.style.height = this.height;
                }
            }
        },

        addResizeListener: function () {
            const observer = new ResizeObserver(() => {
                this.height = this.$el.style.height;
            });
            observer.observe(this.$el);
        },
    }
}
