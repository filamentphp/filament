import { mergeAttributes, Node } from '@tiptap/core'
import { Node as ProseMirrorNode } from '@tiptap/pm/model'
import { Plugin, PluginKey } from '@tiptap/pm/state'
import Suggestion from '@tiptap/suggestion'
import getMentionSuggestion from './mention-suggestion.js'
import normalizeSearchString from '../../../../../support/resources/js/utilities/normalize-search-string.js'

const getSuggestionOptions = function ({
    editor: tiptapEditor,
    overrideSuggestionOptions,
    extensionName,
}) {
    const pluginKey = new PluginKey()

    const triggerCharacter = overrideSuggestionOptions?.triggerCharacter ?? '@'
    const extraAttributes = overrideSuggestionOptions?.extraAttributes ?? {}

    return {
        editor: tiptapEditor,
        char: triggerCharacter,
        pluginKey,
        command: ({ editor, range, props }) => {
            const nodeAfter = editor.view.state.selection.$to.nodeAfter
            const overrideSpace = nodeAfter?.text?.startsWith(' ')

            if (overrideSpace) {
                range.to += 1
            }

            const mentionAttrs = {
                ...props,
                char: triggerCharacter,
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
            getSuggestionFromTriggerCharacter: () => null,
        }
    },

    addOptions() {
        return {
            HTMLAttributes: {},
            renderText({ node }) {
                const triggerCharacter = node.attrs.char ?? '@'
                return `${triggerCharacter}`
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
        ]?.getSuggestionFromTriggerCharacter(node?.attrs?.char ?? '@')

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
            ]?.getSuggestionFromTriggerCharacter(node?.attrs?.char ?? '@'),
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
                        const triggerCharacter = mentionNode?.attrs?.char ?? '@'
                        transaction.insertText(
                            this.options.deleteTriggerWithBackspace
                                ? ''
                                : triggerCharacter,
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
            if (this.editor.isDestroyed) return

            const { state, dispatch } = view
            const pending = []

            state.doc.descendants((node) => {
                if (node.type.name !== this.name) return
                if (node.attrs?.label) return
                const id = node.attrs?.id
                const triggerCharacter = node.attrs?.char ?? '@'
                if (!id) return
                pending.push({ id, triggerCharacter })
            })

            if (pending.length === 0) return

            const getMentionLabelsUsing = this.options.getMentionLabelsUsing
            if (typeof getMentionLabelsUsing !== 'function') return

            try {
                const labels = await getMentionLabelsUsing(pending)
                if (this.editor.isDestroyed) return

                // Positions can now belong to other mentions, or the same mention may have moved.
                const transaction = view.state.tr

                view.state.doc.descendants((node, position) => {
                    if (
                        node.type.name !== this.name ||
                        node.attrs.label ||
                        !pending.some(
                            ({ id, triggerCharacter }) =>
                                id === node.attrs.id &&
                                triggerCharacter === (node.attrs.char ?? '@'),
                        )
                    )
                        return

                    const label =
                        labels[node.attrs.char ?? '@']?.[node.attrs.id]
                    if (!label) return

                    transaction.setNodeMarkup(position, undefined, {
                        ...node.attrs,
                        label,
                    })
                })

                if (transaction.docChanged) dispatch(transaction)
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
            const triggerCharacter = suggestionConfig?.triggerCharacter ?? '@'
            const baseItems = suggestionConfig?.items ?? []
            const noOptionsMessage = suggestionConfig?.noOptionsMessage ?? null
            const noSearchResultsMessage =
                suggestionConfig?.noSearchResultsMessage ?? null
            const isSearchable = suggestionConfig?.isSearchable ?? false
            const getMentionSearchResultsUsing =
                this.options.getMentionSearchResultsUsing

            let suggestion

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
                                        triggerCharacter,
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
                                isSearchable &&
                                typeof getMentionSearchResultsUsing ===
                                    'function'
                            ) {
                                try {
                                    const results =
                                        await getMentionSearchResultsUsing(
                                            query,
                                            triggerCharacter,
                                        )

                                    return toItemsArray(results)
                                } catch {}
                            }

                            if (!query) {
                                return items
                            }

                            const searchQuery = normalizeSearchString(
                                String(query),
                            )

                            return items.filter((item) => {
                                const label =
                                    typeof item === 'string'
                                        ? item
                                        : (item?.label ?? item?.name ?? '')

                                return normalizeSearchString(
                                    String(label),
                                ).includes(searchQuery)
                            })
                        },
                        isSearchable,
                        noOptionsMessage,
                        noSearchResultsMessage,
                        searchPrompt,
                        searchingMessage,
                    }),
                    triggerCharacter,
                    ...(extraAttributes ? { extraAttributes } : {}),
                }
            }

            return getSuggestionOptions({
                editor: this.editor,
                overrideSuggestionOptions: suggestion,
                extensionName: this.name,
            })
        })

        this.storage.getSuggestionFromTriggerCharacter = (triggerCharacter) => {
            return (
                this.storage.suggestions.find(
                    (item) => item.char === triggerCharacter,
                ) ??
                this.storage.suggestions[0] ??
                null
            )
        }
    },
})
