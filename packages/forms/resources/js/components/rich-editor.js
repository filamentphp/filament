import { Editor } from '@tiptap/core'
import getExtensions from './rich-editor/extensions'
import { Selection } from '@tiptap/pm/state'

export default function richEditorFormComponent({
    key,
    livewireId,
    state,
    statePath,
    uploadingFileMessage,
}) {
    let editor

    return {
        state,

        editorSelection: { type: 'text', anchor: 1, head: 1 },

        isUploadingFile: false,

        shouldUpdateState: true,

        editorUpdatedAt: Date.now(),

        init: function () {
            editor = new Editor({
                element: this.$refs.editor,
                extensions: getExtensions({
                    key,
                    statePath,
                    uploadingFileMessage,
                    $wire: this.$wire,
                }),
                content: this.state,
            })

            editor.on('create', ({ editor }) => {
                this.editorUpdatedAt = Date.now()
            })

            editor.on('update', ({ editor }) => {
                this.editorUpdatedAt = Date.now()

                this.state = editor.getJSON()

                this.shouldUpdateState = false
            })

            editor.on('selectionUpdate', ({ editor, transaction }) => {
                this.editorUpdatedAt = Date.now()
                this.editorSelection = transaction.selection.toJSON()
            })

            this.$watch('state', () => {
                if (!this.shouldUpdateState) {
                    this.shouldUpdateState = true

                    return
                }

                editor.commands.setContent(this.state)
            })

            window.addEventListener('run-rich-editor-commands', (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.runEditorCommands(event.detail)
            })

            window.addEventListener('rich-editor-uploading-file', (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = true

                event.stopPropagation()
            })

            window.addEventListener('rich-editor-uploaded-file', (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = false

                event.stopPropagation()
            })

            window.dispatchEvent(
                new CustomEvent(`schema-component-${livewireId}-${key}-loaded`),
            )
        },

        getEditor: function () {
            return editor
        },

        setEditorSelection: function (selection) {
            if (!selection) {
                return
            }

            this.editorSelection = selection

            editor
                .chain()
                .command(({ tr }) => {
                    tr.setSelection(
                        Selection.fromJSON(
                            editor.state.doc,
                            this.editorSelection,
                        ),
                    )

                    return true
                })
                .run()
        },

        runEditorCommands: function ({ commands, editorSelection }) {
            this.setEditorSelection(editorSelection)

            let commandChain = editor.chain()

            commands.forEach(
                (command) =>
                    (commandChain = commandChain[command.name](
                        ...(command.arguments ?? []),
                    )),
            )

            commandChain.run()
        },
    }
}
