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

    const triggerChar = overrideSuggestionOptions?.char ?? '@'
    const extraAttributes = overrideSuggestionOptions?.extraAttributes ?? {}

    return {
        editor: tiptapEditor,
        char: triggerChar,
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
                        attrs: { ...props, char: triggerChar, extra: extraAttributes },
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
            getLabelUsingFromChar: () => null,
        }
    },

    addOptions() {
        return {
            HTMLAttributes: {},
            renderText({ node }) {
                const ch = node.attrs.char ?? '@'
                return `${ch}`
            },
            deleteTriggerWithBackspace: true,
            renderHTML({ options, node }) {
                return [
                    'span',
                    mergeAttributes(this.HTMLAttributes, options.HTMLAttributes),
                    `${node.attrs.char ?? '@'} ${node.attrs.label ?? ''}`,
                ]
            },
            suggestions: [],
            suggestion: {},
            getMentionLabelUsing: null,
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

            char: {
                default: '@',
                parseHTML: (element) => element.getAttribute('data-char') ?? '@',
                renderHTML: (attributes) => {
                    if (!attributes.char) {
                        return {}
                    }

                    return {
                        'data-char': attributes.char,
                    }
                },
            },
            // Arbitrary extra attributes to apply to the element
            extra: {
                default: null,
                renderHTML: (attributes) => {
                    const value = attributes?.extra
                    if (!value || typeof value !== 'object') return {}
                    return value
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
        const suggestion = this.editor?.extensionStorage?.[this.name]?.getSuggestionFromChar(
            node?.attrs?.char ?? '@',
        )

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
            suggestion: this.editor?.extensionStorage?.[this.name]?.getSuggestionFromChar(
                node?.attrs?.char ?? '@',
            ),
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
                        const trigger = mentionNode?.attrs?.char ?? '@'
                        tr.insertText(
                            this.options.deleteTriggerWithBackspace ? '' : trigger,
                            mentionPos,
                            mentionPos + mentionNode.nodeSize,
                        )
                    }

                    return isMention
                }),
        }
    },

    addProseMirrorPlugins() {
        const hydrateMentions = (view) => {
            const { state, dispatch } = view
            const pending = []
            state.doc.descendants((node, pos) => {
                if (node.type.name !== this.name) return
                if (node.attrs?.label) return
                const id = node.attrs?.id
                const ch = node.attrs?.char ?? '@'
                const getLabel = this.editor?.extensionStorage?.[this.name]?.getLabelUsingFromChar(ch) || this.options.getMentionLabelUsing
                if (!id || typeof getLabel !== 'function') return
                pending.push({ id, ch, pos, getLabel })
            })
            pending.forEach(({ id, ch, pos, getLabel }) => {
                Promise.resolve(getLabel(id, ch)).then((label) => {
                    if (!label) return
                    const current = view.state.doc.nodeAt(pos)
                    if (!current || current.type.name !== this.name) return
                    const attrs = { ...current.attrs, label }
                    const tr = view.state.tr.setNodeMarkup(pos, undefined, attrs)
                    dispatch(tr)
                })
            })
        }

        return [
            ...this.storage.suggestions.map(Suggestion),
            new Plugin({
                view: (view) => {
                    // Initial hydration
                    setTimeout(() => hydrateMentions(view), 0)
                    return {
                        update: (view) => hydrateMentions(view),
                    }
                },
            }),
        ]
    },

    onBeforeCreate() {
        const isArrayOfSuggestionObjects = (arr) => Array.isArray(arr) && arr.length > 0 && typeof arr[0] === 'object' && (arr[0].items || arr[0].char)
        const isArrayOfItems = (arr) => Array.isArray(arr) && (arr.length === 0 || (typeof arr[0] === 'string' || typeof arr[0] === 'object'))
        const toItemsArray = (value) => {
            if (value && !Array.isArray(value) && typeof value === 'object') {
                return Object.entries(value).map(([id, label]) => ({ id, label }))
            }
            return value
        }
        const normalizeResults = (results, currentChar, baseItems, query) => {
            if (Array.isArray(results)) {
                if (isArrayOfSuggestionObjects(results)) {
                    const match = results.find((r) => (r?.char ?? '@') === (currentChar ?? '@')) || (results.length === 1 ? results[0] : null)
                    if (match?.items) return toItemsArray(match.items)
                }
                if (isArrayOfItems(results)) return toItemsArray(results)
            }
            if (results && typeof results === 'object') {
                if (results.items) {
                    if (!results.char || results.char === currentChar) {
                        return toItemsArray(results.items)
                    }
                }
                const keys = Object.keys(results)
                if (keys.length && typeof results[keys[0]] !== 'undefined' && !Array.isArray(results[keys[0]])) {
                    return toItemsArray(results)
                }
                const charKey = currentChar ?? '@'
                if (results[charKey]) return toItemsArray(results[charKey])
                const firstKey = keys[0]
                if (firstKey) return toItemsArray(results[firstKey])
            }
            if (!query) return baseItems
            const q = String(query).toLowerCase()
            return (baseItems ?? []).filter((item) => {
                const label = typeof item === 'string' ? item : (item?.label ?? item?.name ?? '')
                return String(label).toLowerCase().includes(q)
            })
        }

        const configured = this.options.suggestions.length ? this.options.suggestions : [this.options.suggestion]

        this.storage.suggestions = configured.map((s) => {
            const char = s?.char ?? '@'
            const baseItems = s?.items ?? []
            const getMentionSearchResultsUsing = this.options.getMentionSearchResultsUsing

            if (typeof s?.items === 'function') {
                const originalItems = s.items
                s = {
                    ...s,
                    items: async (ctx) => {
                        if (typeof getMentionSearchResultsUsing === 'function') {
                            try {
                                const asyncResults = await getMentionSearchResultsUsing(ctx?.query, char)
                                const base = await originalItems(ctx)
                                return normalizeResults(asyncResults, char, base, ctx?.query)
                            } catch (e) {}
                        }
                        return await originalItems(ctx)
                    },
                }
            } else {
                s = {
                    ...getMentionSuggestion({
                        items: async ({ query }) => {
                            if (typeof getMentionSearchResultsUsing === 'function') {
                                try {
                                    const asyncResults = await getMentionSearchResultsUsing(query, char)
                                    return normalizeResults(asyncResults, char, baseItems, query)
                                } catch (e) {}
                            }
                            const base = baseItems
                            if (!query) return base
                            const q = String(query).toLowerCase()
                            return (base ?? []).filter((item) => {
                                const label = typeof item === 'string' ? item : (item?.label ?? item?.name ?? '')
                                return String(label).toLowerCase().includes(q)
                            })
                        },
                    }),
                    char,
                }
            }

            // Attach per-char label resolver if provided on options
            if (typeof this.options.getMentionLabelUsing === 'function') {
                s.getMentionLabelUsing = (id) => this.options.getMentionLabelUsing(id, char)
            }

            return getSuggestionOptions({
                editor: this.editor,
                overrideSuggestionOptions: s,
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

        this.storage.getLabelUsingFromChar = (char) => {
            const suggestion = this.storage.getSuggestionFromChar(char)
            if (suggestion && typeof suggestion.getMentionLabelUsing === 'function') {
                return suggestion.getMentionLabelUsing
            }
            return null
        }
    },
})


