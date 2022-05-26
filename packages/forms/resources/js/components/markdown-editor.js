import '../../css/components/markdown-editor.css'
import EasyMDE from 'easymde'

export default (Alpine) => {
    Alpine.data('markdownEditorFormComponent', ({
        state,
        tab,
        darkMode
    }) => {
        return {
            preview: '',

            state,

            tab,

            editor: null,

            isStateBeingUpdated: false,

            init: function () {
                this.editor = new EasyMDE({
                    element: this.$refs.textarea,
                    spellChecker: false,
                    status: false,
                    initialValue: this.state,
                    toolbar: ['upload-image'],
                    styleSelectedText: false,
                    blockStyles: {
                        italic: '_',
                    },
                    unorderedListStyle: '-',
                    previewClass: `prose w-full h-full max-w-none bg-white p-3 ${darkMode ? 'dark:prose-invert dark:bg-gray-700' : ''}`.trim(),
                    minHeight: '150px',
                })

                this.editor.codemirror.on('change', () => {
                    this.isStateBeingUpdated = true
                    this.state = this.editor.value()
                    this.$nextTick(() => this.isStateBeingUpdated = false)
                })

                this.$watch('tab', () => {
                    if (this.tab === 'preview' && this.editor.isPreviewActive() || this.tab === 'write' && ! this.editor.isPreviewActive()) {
                        return
                    }

                    this.editor.togglePreview()
                })

                this.$watch('state', () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.editor.value(this.state)
                })
            }
        }
    })
}
