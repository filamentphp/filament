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
                    previewClass: `editor-preview-side editor-preview prose max-w-none rounded-lg border border-gray-300 bg-white p-3 shadow-sm ${darkMode ? 'dark:prose-invert dark:border-gray-600 dark:bg-gray-700' : ''}`.trim()
                })

                this.editor.codemirror.on('change', () => {
                    this.isStateBeingUpdated = true
                    this.state = this.editor.value()
                    this.$nextTick(() => this.isStateBeingUpdated = false)
                })

                this.$watch('state', () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.editor.value(this.state)
                })
            },
        }
    })
}
