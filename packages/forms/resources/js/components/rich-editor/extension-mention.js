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

            const mentionAttrs = {
                ...props,
                char: triggerChar,
                extra: extraAttributes,
            }

            editor
                .chain()
                .focus()
                .insertContentAt(range, [
                    {
                        type: extensionName,
                        attrs: mentionAttrs,
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
                const char = node.attrs.char ?? '@'
                return `${char}`
            },
            deleteTriggerWithBackspace: true,
            renderHTML({ options, node }) {
                return [
                    'span',
                    mergeAttributes(
                        this.HTMLAttributes,
                        options.HTMLAttributes,
                    ),
                    `${node.attrs.char ?? '@'}${node.attrs.label ?? ''}`,
                ]
            },
            suggestions: [],
            suggestion: {},
            getMentionLabelsUsing: null,
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
                keepOnSplit: false,
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
                parseHTML: (element) =>
                    element.getAttribute('data-char') ?? '@',
                renderHTML: (attributes) => {
                    if (!attributes.char) {
                        return {}
                    }

                    return {
                        'data-char': attributes.char,
                    }
                },
            },
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
        const suggestion = this.editor?.extensionStorage?.[
            this.name
        ]?.getSuggestionFromChar(node?.attrs?.char ?? '@')

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
            suggestion: this.editor?.extensionStorage?.[
                this.name
            ]?.getSuggestionFromChar(node?.attrs?.char ?? '@'),
        }
        return this.options.renderText(args)
    },

    addKeyboardShortcuts() {
        return {
            Backspace: () =>
                this.editor.commands.command(({ tr: transaction, state }) => {
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
                        transaction.insertText(
                            this.options.deleteTriggerWithBackspace
                                ? ''
                                : trigger,
                            mentionPos,
                            mentionPos + mentionNode.nodeSize,
                        )
                    }

                    return isMention
                }),
        }
    },

    addProseMirrorPlugins() {
        const hydrateMentions = async (view) => {
            const { state, dispatch } = view
            const pending = []

            state.doc.descendants((node, pos) => {
                if (node.type.name !== this.name) return
                if (node.attrs?.label) return
                const id = node.attrs?.id
                const char = node.attrs?.char ?? '@'
                if (!id) return
                pending.push({ id, char, pos })
            })

            if (pending.length === 0) return

            const getMentionLabelsUsing = this.options.getMentionLabelsUsing
            if (typeof getMentionLabelsUsing !== 'function') return

            try {
                const mentions = pending.map(({ id, char }) => ({ id, char }))
                const labels = await getMentionLabelsUsing(mentions)

                pending.forEach(({ id, pos }) => {
                    const label = labels[id]
                    if (!label) return
                    const current = view.state.doc.nodeAt(pos)
                    if (!current || current.type.name !== this.name) return
                    const attrs = { ...current.attrs, label }
                    const transaction = view.state.tr.setNodeMarkup(
                        pos,
                        undefined,
                        attrs,
                    )
                    dispatch(transaction)
                })
            } catch {}
        }

        return [
            ...this.storage.suggestions.map(Suggestion),
            new Plugin({
                view: (view) => {
                    setTimeout(() => hydrateMentions(view), 0)
                    return {
                        update: (view) => hydrateMentions(view),
                    }
                },
            }),
        ]
    },

    onBeforeCreate() {
        const toItemsArray = (value) => {
            if (Array.isArray(value)) {
                return value
            }

            if (value && typeof value === 'object') {
                return Object.entries(value).map(([id, label]) => ({
                    id,
                    label,
                }))
            }

            return []
        }

        const configured = this.options.suggestions.length
            ? this.options.suggestions
            : [this.options.suggestion]

        this.storage.suggestions = configured.map((suggestionConfig) => {
            const char = suggestionConfig?.char ?? '@'
            const baseItems = suggestionConfig?.items ?? []
            const noOptionsMessage = suggestionConfig?.noOptionsMessage ?? null
            const noSearchResultsMessage =
                suggestionConfig?.noSearchResultsMessage ?? null
            const isSearchable = suggestionConfig?.isSearchable ?? false
            const getMentionSearchResultsUsing =
                this.options.getMentionSearchResultsUsing

            let suggestion = suggestionConfig

            if (typeof suggestionConfig?.items === 'function') {
                const originalItems = suggestionConfig.items

                suggestion = {
                    ...suggestionConfig,
                    items: async (context) => {
                        if (
                            context?.query &&
                            typeof getMentionSearchResultsUsing === 'function'
                        ) {
                            try {
                                const results =
                                    await getMentionSearchResultsUsing(
                                        context?.query,
                                        char,
                                    )

                                return toItemsArray(results)
                            } catch {}
                        }

                        return await originalItems(context)
                    },
                }
            } else {
                const extraAttributes = suggestionConfig?.extraAttributes
                const searchPrompt = suggestionConfig?.searchPrompt ?? null
                const searchingMessage =
                    suggestionConfig?.searchingMessage ?? null

                suggestion = {
                    ...getMentionSuggestion({
                        items: async ({ query }) => {
                            const hasBaseItems = Array.isArray(baseItems)
                                ? baseItems.length > 0
                                : baseItems &&
                                  typeof baseItems === 'object' &&
                                  Object.keys(baseItems).length > 0

                            if (!hasBaseItems && !query) {
                                return []
                            }

                            const items = toItemsArray(baseItems)

                            if (
                                query &&
                                typeof getMentionSearchResultsUsing ===
                                    'function'
                            ) {
                                try {
                                    const results =
                                        await getMentionSearchResultsUsing(
                                            query,
                                            char,
                                        )

                                    return toItemsArray(results)
                                } catch {}
                            }

                            if (!query) {
                                return items
                            }

                            const searchQuery = String(query).toLowerCase()

                            return items.filter((item) => {
                                const label =
                                    typeof item === 'string'
                                        ? item
                                        : (item?.label ?? item?.name ?? '')

                                return String(label)
                                    .toLowerCase()
                                    .includes(searchQuery)
                            })
                        },
                        isSearchable,
                        noOptionsMessage,
                        noSearchResultsMessage,
                        searchPrompt,
                        searchingMessage,
                    }),
                    char,
                    ...(extraAttributes ? { extraAttributes } : {}),
                }
            }

            return getSuggestionOptions({
                editor: this.editor,
                overrideSuggestionOptions: suggestion,
                extensionName: this.name,
            })
        })

        this.storage.getSuggestionFromChar = (char) => {
            return (
                this.storage.suggestions.find((item) => item.char === char) ??
                this.storage.suggestions[0] ??
                null
            )
        }
    },
})
