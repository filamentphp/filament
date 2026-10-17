export default () => ({
    getTooltip({ content, allowHTML }) {
        return this.$store.sidebar.isOpen
            ? false
            : {
                  content,
                  allowHTML,
                  placement: document.dir === 'rtl' ? 'left' : 'right',
                  theme: this.$store.theme,
              }
    },
})
