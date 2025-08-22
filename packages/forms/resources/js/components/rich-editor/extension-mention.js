import { mergeAttributes, Node } from '@tiptap/core'
import { Node as ProseMirrorNode } from '@tiptap/pm/model'
import { Plugin, PluginKey } from '@tiptap/pm/state'
import Suggestion from '@tiptap/suggestion'
import getMentionSuggestion from './mention-suggestion.js'

const getSuggestionOptions = function ({
    editor: tiptapEditor,
    overrideSuggestionOptions,
    extensionName,
}) {
    const pluginKey = new PluginKey()

    return {
        editor: tiptapEditor,
        char: '@',
        pluginKey,
        command: ({ editor, range, props }) => {
            const nodeAfter = editor.view.state.selection.$to.nodeAfter
            const overrideSpace = nodeAfter?.text?.startsWith(' ')

            if (overrideSpace) {
                range.to += 1
            }

            editor
                .chain()
                .focus()
                .insertContentAt(range, [
                    {
                        type: extensionName,
                        attrs: { ...props },
                    },
                    {
                        type: 'text',
                        text: ' ',
                    },
                ])
                .run()

            editor.view.dom.ownerDocument.defaultView
                ?.getSelection()
                ?.collapseToEnd()
        },
        allow: ({ state, range }) => {
            const $from = state.doc.resolve(range.from)
            const type = state.schema.nodes[extensionName]
            const allow = !!$from.parent.type.contentMatch.matchType(type)

            return allow
        },
        ...overrideSuggestionOptions,
    }
}

export default Node.create({
    name: 'mention',

    priority: 101,

    addStorage() {
        return {
            suggestions: [],
            getSuggestionFromChar: () => null,
        }
    },

    addOptions() {
        return {
            HTMLAttributes: {},
            renderText({ node }) {
                return `@${node.attrs.label ?? node.attrs.id}`
            },
            deleteTriggerWithBackspace: false,
            renderHTML({ options, node }) {
                return [
                    'span',
                    mergeAttributes(this.HTMLAttributes, options.HTMLAttributes),
                    `@${node.attrs.label ?? node.attrs.id}`,
                ]
            },
            suggestions: [],
            suggestion: {},
        }
    },

    group: 'inline',

    inline: true,

    selectable: false,

    atom: true,

    addAttributes() {
        return {
            id: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-id'),
                renderHTML: (attributes) => {
                    if (!attributes.id) {
                        return {}
                    }

                    return {
                        'data-id': attributes.id,
                    }
                },
            },

            label: {
                default: null,
                parseHTML: (element) => element.getAttribute('data-label'),
                renderHTML: (attributes) => {
                    if (!attributes.label) {
                        return {}
                    }

                    return {
                        'data-label': attributes.label,
                    }
                },
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: `span[data-type="${this.name}"]`,
            },
        ]
    },

    renderHTML({ node, HTMLAttributes }) {
        const suggestion = this.editor?.extensionStorage?.[this.name]?.getSuggestionFromChar('@')

        const mergedOptions = { ...this.options }

        mergedOptions.HTMLAttributes = mergeAttributes(
            { 'data-type': this.name },
            this.options.HTMLAttributes,
            HTMLAttributes,
        )

        const html = this.options.renderHTML({
            options: mergedOptions,
            node,
            suggestion,
        })

        if (typeof html === 'string') {
            return [
                'span',
                mergeAttributes(
                    { 'data-type': this.name },
                    this.options.HTMLAttributes,
                    HTMLAttributes,
                ),
                html,
            ]
        }
        return html
    },

    renderText({ node }) {
        const args = {
            options: this.options,
            node,
            suggestion: this.editor?.extensionStorage?.[this.name]?.getSuggestionFromChar('@'),
        }

        return this.options.renderText(args)
    },

    addKeyboardShortcuts() {
        return {
            Backspace: () =>
                this.editor.commands.command(({ tr, state }) => {
                    let isMention = false
                    const { selection } = state
                    const { empty, anchor } = selection

                    if (!empty) {
                        return false
                    }

                    let mentionNode = new ProseMirrorNode()
                    let mentionPos = 0

                    state.doc.nodesBetween(anchor - 1, anchor, (node, pos) => {
                        if (node.type.name === this.name) {
                            isMention = true
                            mentionNode = node
                            mentionPos = pos
                            return false
                        }
                    })

                    if (isMention) {
                        tr.insertText(
                            this.options.deleteTriggerWithBackspace ? '' : '@',
                            mentionPos,
                            mentionPos + mentionNode.nodeSize,
                        )
                    }

                    return isMention
                }),
        }
    },

    addProseMirrorPlugins() {
        return [
            ...this.storage.suggestions.map(Suggestion),
            new Plugin({}),
        ]
    },

    onBeforeCreate() {
        this.storage.suggestions = (
            this.options.suggestions.length ? this.options.suggestions : [this.options.suggestion]
        ).map((suggestion) => {
            const normalized = typeof suggestion.items === 'function' || typeof suggestion.render === 'function'
                ? suggestion
                : getMentionSuggestion({ items: suggestion.items ?? [] })

            return getSuggestionOptions({
                editor: this.editor,
                overrideSuggestionOptions: normalized,
                extensionName: this.name,
            })
        })

        this.storage.getSuggestionFromChar = (char) => {
            const suggestion = this.storage.suggestions.find((s) => s.char === char)
            if (suggestion) {
                return suggestion
            }
            if (this.storage.suggestions.length) {
                return this.storage.suggestions[0]
            }

            return null
        }
    },
})


