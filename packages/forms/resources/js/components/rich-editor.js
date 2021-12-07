import Trix from 'trix/dist/trix'

import 'trix/dist/trix.css'

Trix.config.blockAttributes.heading = {
    tagName: "h2",
    terminal: true,
    breakOnReturn: true,
    group: false
}

Trix.config.blockAttributes.subHeading = {
    tagName: "h3",
    terminal: true,
    breakOnReturn: true,
    group: false
}

export default (Alpine) => {
    Alpine.data('richEditorFormComponent', ({
        state,
    }) => {
        return {
            state,

            init: function () {
                this.$refs.trix?.editor?.loadHTML(this.state)

                this.$watch('state', () => {
                    if (document.activeElement === this.$refs.trix) {
                        return
                    }

                    this.$refs.trix?.editor?.loadHTML(this.state)
                })
            }
        }
    })
}
