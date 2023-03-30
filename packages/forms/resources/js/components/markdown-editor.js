import '@github/file-attachment-element'
import '@github/markdown-toolbar-element'
import DOMPurify from 'dompurify'
import { marked } from 'marked'
import { highlight } from 'mdhl'

export default (Alpine) => {
    Alpine.data('markdownEditorFormComponent', ({ state, tab }) => {
        return {
            attachment: null,

            overlay: null,

            preview: '',

            state,

            tab,

            init: function () {
                if (this.state !== null) {
                    this.$nextTick(() => {
                        this.render()
                    })
                }

                this.$watch('state', (value) => {
                    if (value === null) {
                        this.state = ''
                    }

                    this.render()
                })
            },

            render: function () {
                if ((this.$refs.textarea?.scrollHeight ?? 0) > 0) {
                    this.$refs.overlay.style.height = '150px'
                    this.$refs.overlay.style.height =
                        this.$refs.textarea.scrollHeight + 'px'
                }

                this.state = this.state.replace('\r\n', '\n')

                this.overlay = null
                this.overlay = highlight(this.state)

                this.preview = null
                this.preview = DOMPurify.sanitize(marked(this.state))
            },

            checkForAutoInsertion: function () {
                const lines = this.$refs.textarea.value.split('\n')

                const linesBeforeSelection =
                    this.$refs.textarea.value.substring(
                        0,
                        this.$refs.textarea.selectionStart,
                    )

                const currentLine = linesBeforeSelection.split('\n').length
                const previousLine = lines[currentLine - 2]

                if (!previousLine.match(/^(\*\s|-\s)|^(\d)+\./)) {
                    return
                }

                if (previousLine.match(/^(\*\s)/)) {
                    if (previousLine.trim().length > 1) {
                        lines[currentLine - 1] = '* '

                        this.$nextTick(() => {
                            this.$refs.textarea.selectionStart =
                                this.$refs.textarea.selectionEnd =
                                    linesBeforeSelection.length + 2
                        })
                    } else {
                        delete lines[currentLine - 2]

                        this.$nextTick(() => {
                            this.$refs.textarea.selectionStart =
                                this.$refs.textarea.selectionEnd =
                                    linesBeforeSelection.length - 2
                        })
                    }
                } else if (previousLine.match(/^(-\s)/)) {
                    if (previousLine.trim().length > 1) {
                        lines[currentLine - 1] = '- '

                        this.$nextTick(() => {
                            this.$refs.textarea.selectionStart =
                                this.$refs.textarea.selectionEnd =
                                    linesBeforeSelection.length + 2
                        })
                    } else {
                        delete lines[currentLine - 2]

                        this.$nextTick(() => {
                            this.$refs.textarea.selectionStart =
                                this.$refs.textarea.selectionEnd =
                                    linesBeforeSelection.length - 2
                        })
                    }
                } else {
                    const matches = previousLine.match(/^(\d)+/)
                    const number = matches[0]

                    if (previousLine.trim().length > number.length + 2) {
                        lines[currentLine - 1] = `${parseInt(number) + 1}. `

                        this.$nextTick(() => {
                            this.$refs.textarea.selectionStart =
                                this.$refs.textarea.selectionEnd =
                                    linesBeforeSelection.length +
                                    number.length +
                                    2
                        })
                    } else {
                        delete lines[currentLine - 2]

                        this.$nextTick(() => {
                            this.$refs.textarea.selectionStart =
                                this.$refs.textarea.selectionEnd =
                                    linesBeforeSelection.length -
                                    number.length -
                                    2
                        })
                    }
                }

                this.state = lines.join('\n')

                this.render()
            },
        }
    })
}
