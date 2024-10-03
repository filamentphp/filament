import Trix from 'trix'

Trix.config.blockAttributes.default.tagName = 'p'

Trix.config.blockAttributes.default.breakOnReturn = true

Trix.config.blockAttributes.heading = {
    tagName: 'h2',
    terminal: true,
    breakOnReturn: true,
    group: false,
}

Trix.config.blockAttributes.subHeading = {
    tagName: 'h3',
    terminal: true,
    breakOnReturn: true,
    group: false,
}

Trix.config.textAttributes.underline = {
    style: { textDecoration: 'underline' },
    inheritable: true,
    parser: (element) => {
        const style = window.getComputedStyle(element)

        return style.textDecoration.includes('underline')
    },
}

Trix.Block.prototype.breaksOnReturn = function () {
    const lastAttribute = this.getLastAttribute()
    const blockConfig =
        Trix.config.blockAttributes[lastAttribute ? lastAttribute : 'default']

    return blockConfig?.breakOnReturn ?? false
}

Trix.LineBreakInsertion.prototype.shouldInsertBlockBreak = function () {
    if (
        this.block.hasAttributes() &&
        this.block.isListItem() &&
        !this.block.isEmpty()
    ) {
        return this.startLocation.offset > 0
    } else {
        return !this.shouldBreakFormattedBlock() ? this.breaksOnReturn : false
    }
}

export default function richEditorFormComponent({ state }) {
    return {
        state,

        init: function () {
            this.$refs.trixValue.value = this.state
            this.$refs.trix.editor?.loadHTML(this.state ?? '')

            this.$watch('state', () => {
                if (document.activeElement === this.$refs.trix) {
                    return
                }

                this.$refs.trixValue.value = this.state
                this.$refs.trix.editor?.loadHTML(this.state ?? '')
            })
        },
    }
}
