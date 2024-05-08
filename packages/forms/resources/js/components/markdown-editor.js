const {
    getSentenceDifficulty,
    getPassivePhrases,
    getAdverbPhrases,
    getComplexPhrases,
    getQualifiedPhrases,
} = require('@assertchris/ellison')

window.CodeMirror = require('codemirror/lib/codemirror')

require('codemirror')
require('codemirror/addon/mode/overlay')
require('codemirror/addon/edit/continuelist')
require('codemirror/addon/display/placeholder')
require('codemirror/addon/selection/mark-selection')
require('codemirror/addon/search/searchcursor')
require('codemirror/mode/clike/clike')
require('codemirror/mode/cmake/cmake')
require('codemirror/mode/css/css')
require('codemirror/mode/diff/diff')
require('codemirror/mode/django/django')
require('codemirror/mode/dockerfile/dockerfile')
require('codemirror/mode/gfm/gfm')
require('codemirror/mode/go/go')
require('codemirror/mode/htmlmixed/htmlmixed')
require('codemirror/mode/http/http')
require('codemirror/mode/javascript/javascript')
require('codemirror/mode/jinja2/jinja2')
require('codemirror/mode/jsx/jsx')
require('codemirror/mode/markdown/markdown')
require('codemirror/mode/nginx/nginx')
require('codemirror/mode/pascal/pascal')
require('codemirror/mode/perl/perl')
require('codemirror/mode/php/php')
require('codemirror/mode/protobuf/protobuf')
require('codemirror/mode/python/python')
require('codemirror/mode/ruby/ruby')
require('codemirror/mode/rust/rust')
require('codemirror/mode/sass/sass')
require('codemirror/mode/shell/shell')
require('codemirror/mode/sql/sql')
require('codemirror/mode/stylus/stylus')
require('codemirror/mode/swift/swift')
require('codemirror/mode/vue/vue')
require('codemirror/mode/xml/xml')
require('codemirror/mode/yaml/yaml')

require('./markdown-editor/EasyMDE')

CodeMirror.commands.tabAndIndentMarkdownList = function (codemirror) {
    var ranges = codemirror.listSelections()
    var pos = ranges[0].head
    var eolState = codemirror.getStateAfter(pos.line)
    var inList = eolState.list !== false

    if (inList) {
        codemirror.execCommand('indentMore')
        return
    }

    if (codemirror.options.indentWithTabs) {
        codemirror.execCommand('insertTab')

        return
    }

    var spaces = Array(codemirror.options.tabSize + 1).join(' ')
    codemirror.replaceSelection(spaces)
}

CodeMirror.commands.shiftTabAndUnindentMarkdownList = function (codemirror) {
    var ranges = codemirror.listSelections()
    var pos = ranges[0].head
    var eolState = codemirror.getStateAfter(pos.line)
    var inList = eolState.list !== false

    if (inList) {
        codemirror.execCommand('indentLess')

        return
    }

    if (codemirror.options.indentWithTabs) {
        codemirror.execCommand('insertTab')

        return
    }

    var spaces = Array(codemirror.options.tabSize + 1).join(' ')
    codemirror.replaceSelection(spaces)
}

export default function markdownEditorFormComponent({
    canAttachFiles,
    isLiveDebounced,
    isLiveOnBlur,
    liveDebounce,
    maxHeight,
    minHeight,
    placeholder,
    hasLanguageAssistance,
    state,
    translations,
    toolbarButtons,
    uploadFileAttachmentUsing,
}) {
    return {
        editor: null,

        state,

        moderateSentences: 0,
        complexSentences: 0,
        passivePhrases: 0,
        adverbPhrases: 0,
        complexPhrases: 0,
        qualifiedPhrases: 0,

        init: async function () {
            if (this.$root._editor) {
                this.$root._editor.toTextArea()
                this.$root._editor = null
            }

            this.$root._editor = this.editor = new EasyMDE({
                autoDownloadFontAwesome: false,
                autoRefresh: true,
                autoSave: false,
                element: this.$refs.editor,
                imageAccept: 'image/png, image/jpeg, image/gif, image/avif',
                imageUploadFunction: uploadFileAttachmentUsing,
                initialValue: this.state ?? '',
                maxHeight,
                minHeight,
                placeholder,
                previewImagesInEditor: true,
                spellChecker: false,
                status: [
                    {
                        className: 'upload-image',
                        defaultValue: '',
                    },
                ],
                toolbar: this.getToolbar(),
                uploadImage: canAttachFiles,
            })

            this.editor.codemirror.setOption(
                'direction',
                document.documentElement?.dir ?? 'ltr',
            )

            // When creating a link, highlight the URL instead of the label:
            this.editor.codemirror.on('changes', (instance, changes) => {
                try {
                    const lastChange = changes[changes.length - 1]

                    if (lastChange.origin === '+input') {
                        const urlPlaceholder = '(https://)'
                        const urlLineText =
                            lastChange.text[lastChange.text.length - 1]

                        if (
                            urlLineText.endsWith(urlPlaceholder) &&
                            urlLineText !== '[]' + urlPlaceholder
                        ) {
                            const from = lastChange.from
                            const to = lastChange.to
                            const isSelectionMultiline =
                                lastChange.text.length > 1
                            const baseIndex = isSelectionMultiline ? 0 : from.ch

                            setTimeout(() => {
                                instance.setSelection(
                                    {
                                        line: to.line,
                                        ch:
                                            baseIndex +
                                            urlLineText.lastIndexOf('(') +
                                            1,
                                    },
                                    {
                                        line: to.line,
                                        ch:
                                            baseIndex +
                                            urlLineText.lastIndexOf(')'),
                                    },
                                )
                            }, 25)
                        }
                    }
                } catch (error) {
                    // Revert to original behaviour.
                }
            })

            this.editor.codemirror.on(
                'change',
                Alpine.debounce(() => {
                    if (!this.editor) {
                        return
                    }

                    this.state = this.editor.value()

                    if (isLiveDebounced) {
                        this.$wire.call('$refresh')
                    }
                }, liveDebounce ?? 300),
            )

            if (isLiveOnBlur) {
                this.editor.codemirror.on('blur', () =>
                    this.$wire.call('$refresh'),
                )
            }

            if (hasLanguageAssistance) {
                let oldValue = null
                const markers = []

                const debounce = (func, timeout = 300) => {
                    let timer

                    return (...args) => {
                        clearTimeout(timer)
                        timer = setTimeout(() => {
                            func.apply(this, args)
                        }, timeout)
                    }
                }

                const getAllIndices = (str, val) => {
                    const indices = []
                    let i = -1

                    while ((i = str.indexOf(val, i + 1)) !== -1) {
                        indices.push(i)
                    }

                    return indices
                }

                const checkLanguage = (cm) => {
                    const newValue = cm.getValue()

                    if (oldValue === newValue) {
                        return
                    }

                    this.moderateSentences = 0
                    this.complexSentences = 0
                    this.passivePhrases = 0
                    this.adverbPhrases = 0
                    this.complexPhrases = 0
                    this.qualifiedPhrases = 0

                    while (markers.length > 0) {
                        try {
                            markers.pop().clear()
                        } catch (e) {
                            console.warn(e)
                        }
                    }

                    oldValue = newValue

                    const lines = cm.doc.children[0].lines

                    for (let line of lines) {
                        let sentenceOffset = 0

                        const sentences = getSentenceDifficulty(line.text)
                        const lineNumber = cm.getLineNumber(line)
                        const lineElement =
                            this.$refs.editor.parentNode.parentNode.querySelectorAll(
                                '.CodeMirror-line',
                            )[lineNumber]

                        for (let sentence of sentences) {
                            if (sentence.type === 'moderate') {
                                this.moderateSentences++
                            }

                            if (sentence.type === 'complex') {
                                this.complexSentences++
                            }

                            const sentenceText = sentence.text.trim()

                            if (
                                lineElement.innerText.indexOf(sentenceText) < 0
                            ) {
                                continue
                            }

                            const offset = lineElement.innerText.indexOf(
                                sentenceText,
                                sentenceOffset,
                            )

                            const params = [
                                {
                                    line: lineNumber,
                                    ch: lineElement.innerText.indexOf(
                                        sentenceText,
                                        sentenceOffset,
                                    ),
                                },
                                {
                                    line: lineNumber,
                                    ch:
                                        lineElement.innerText.indexOf(
                                            sentenceText,
                                            sentenceOffset,
                                        ) + sentenceText.length,
                                },
                                {
                                    className: `language-${sentence.type}-sentence`,
                                },
                            ]

                            sentenceOffset = offset + sentenceText.length

                            markers.push(cm.markText(...params))

                            const problems = [
                                ...getAdverbPhrases(sentenceText),
                                ...getPassivePhrases(sentenceText),
                                ...getComplexPhrases(sentenceText),
                                ...getQualifiedPhrases(sentenceText),
                            ]

                            for (let problem of problems) {
                                if (problem.type === 'adverb') {
                                    this.adverbPhrases++
                                }

                                if (problem.type === 'passive') {
                                    this.passivePhrases++
                                }

                                if (problem.type === 'complex') {
                                    this.complexPhrases++
                                }

                                if (problem.type === 'qualified') {
                                    this.qualifiedPhrases++
                                }

                                const indices = getAllIndices(
                                    lineElement.innerText.toLowerCase(),
                                    problem.text.toLowerCase(),
                                )

                                for (let index of indices) {
                                    const params = [
                                        {
                                            line: lineNumber,
                                            ch: index,
                                        },
                                        {
                                            line: lineNumber,
                                            ch: index + problem.text.length,
                                        },
                                        {
                                            className: `language-${problem.type}-phrase`,
                                        },
                                    ]

                                    markers.push(cm.markText(...params))
                                }
                            }
                        }
                    }
                }

                this.editor.codemirror.on('update', debounce(checkLanguage))
            }

            this.$watch('state', () => {
                if (!this.editor) {
                    return
                }

                if (this.editor.codemirror.hasFocus()) {
                    return
                }

                Alpine.raw(this.editor).value(this.state ?? '')
            })
        },

        destroy: function () {
            this.editor.cleanup()
            this.editor = null
        },

        getToolbar: function () {
            let toolbar = []

            if (toolbarButtons.includes('bold')) {
                toolbar.push({
                    name: 'bold',
                    action: EasyMDE.toggleBold,
                    title: translations.toolbar_buttons?.bold,
                })
            }

            if (toolbarButtons.includes('italic')) {
                toolbar.push({
                    name: 'italic',
                    action: EasyMDE.toggleItalic,
                    title: translations.toolbar_buttons?.italic,
                })
            }

            if (toolbarButtons.includes('strike')) {
                toolbar.push({
                    name: 'strikethrough',
                    action: EasyMDE.toggleStrikethrough,
                    title: translations.toolbar_buttons?.strike,
                })
            }

            if (toolbarButtons.includes('link')) {
                toolbar.push({
                    name: 'link',
                    action: EasyMDE.drawLink,
                    title: translations.toolbar_buttons?.link,
                })
            }

            if (
                ['bold', 'italic', 'strike', 'link'].some((button) =>
                    toolbarButtons.includes(button),
                ) &&
                ['heading'].some((button) => toolbarButtons.includes(button))
            ) {
                toolbar.push('|')
            }

            if (toolbarButtons.includes('heading')) {
                toolbar.push({
                    name: 'heading',
                    action: EasyMDE.toggleHeadingSmaller,
                    title: translations.toolbar_buttons?.heading,
                })
            }

            if (
                ['heading'].some((button) => toolbarButtons.includes(button)) &&
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'].some(
                    (button) => toolbarButtons.includes(button),
                )
            ) {
                toolbar.push('|')
            }

            if (toolbarButtons.includes('blockquote')) {
                toolbar.push({
                    name: 'quote',
                    action: EasyMDE.toggleBlockquote,
                    title: translations.toolbar_buttons?.blockquote,
                })
            }

            if (toolbarButtons.includes('codeBlock')) {
                toolbar.push({
                    name: 'code',
                    action: EasyMDE.toggleCodeBlock,
                    title: translations.toolbar_buttons?.code_block,
                })
            }

            if (toolbarButtons.includes('bulletList')) {
                toolbar.push({
                    name: 'unordered-list',
                    action: EasyMDE.toggleUnorderedList,
                    title: translations.toolbar_buttons?.bullet_list,
                })
            }

            if (toolbarButtons.includes('orderedList')) {
                toolbar.push({
                    name: 'ordered-list',
                    action: EasyMDE.toggleOrderedList,
                    title: translations.toolbar_buttons?.ordered_list,
                })
            }

            if (
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'].some(
                    (button) => toolbarButtons.includes(button),
                ) &&
                ['table', 'attachFiles'].some((button) =>
                    toolbarButtons.includes(button),
                )
            ) {
                toolbar.push('|')
            }

            if (toolbarButtons.includes('table')) {
                toolbar.push({
                    name: 'table',
                    action: EasyMDE.drawTable,
                    title: translations.toolbar_buttons?.table,
                })
            }

            if (toolbarButtons.includes('attachFiles')) {
                toolbar.push({
                    name: 'upload-image',
                    action: EasyMDE.drawUploadedImage,
                    title: translations.toolbar_buttons?.attach_files,
                })
            }

            if (
                ['table', 'attachFiles'].some((button) =>
                    toolbarButtons.includes(button),
                ) &&
                ['undo', 'redo'].some((button) =>
                    toolbarButtons.includes(button),
                )
            ) {
                toolbar.push('|')
            }

            if (toolbarButtons.includes('undo')) {
                toolbar.push({
                    name: 'undo',
                    action: EasyMDE.undo,
                    title: translations.toolbar_buttons?.undo,
                })
            }

            if (toolbarButtons.includes('redo')) {
                toolbar.push({
                    name: 'redo',
                    action: EasyMDE.redo,
                    title: translations.toolbar_buttons?.redo,
                })
            }

            return toolbar
        },
    }
}
