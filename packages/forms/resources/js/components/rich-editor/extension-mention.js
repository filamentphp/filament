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
                const char = node.attrs.char ?? '@'
                const label = node.attrs.label ?? ''
                // Use non-breaking space to prevent wrapping between char and label
                const content = `${char}\u00A0${label}`

                return [
                    'span',
                    mergeAttributes(
                        this.HTMLAttributes,
                        options.HTMLAttributes,
                    ),
                    content,
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
        const isArrayOfSuggestionObjects = (arr) =>
            Array.isArray(arr) &&
            arr.length > 0 &&
            typeof arr[0] === 'object' &&
            (arr[0].items || arr[0].char)
        const isArrayOfItems = (arr) =>
            Array.isArray(arr) &&
            (arr.length === 0 ||
                typeof arr[0] === 'string' ||
                typeof arr[0] === 'object')
        const toItemsArray = (value) => {
            if (value && !Array.isArray(value) && typeof value === 'object') {
                return Object.entries(value).map(([id, label]) => ({
                    id,
                    label,
                }))
            }
            return value
        }
        const normalizeResults = (results, currentChar, baseItems, query) => {
            if (Array.isArray(results)) {
                if (isArrayOfSuggestionObjects(results)) {
                    const match =
                        results.find(
                            (result) =>
                                (result?.char ?? '@') === (currentChar ?? '@'),
                        ) || (results.length === 1 ? results[0] : null)
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
                if (
                    keys.length &&
                    typeof results[keys[0]] !== 'undefined' &&
                    !Array.isArray(results[keys[0]])
                ) {
                    return toItemsArray(results)
                }
                const charKey = currentChar ?? '@'
                if (results[charKey]) return toItemsArray(results[charKey])
                const firstKey = keys[0]
                if (firstKey) return toItemsArray(results[firstKey])
            }
            if (!query) return baseItems
            const searchQuery = String(query).toLowerCase()
            return (baseItems ?? []).filter((item) => {
                const label =
                    typeof item === 'string'
                        ? item
                        : (item?.label ?? item?.name ?? '')
                return String(label).toLowerCase().includes(searchQuery)
            })
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
                            typeof getMentionSearchResultsUsing === 'function'
                        ) {
                            try {
                                const asyncResults =
                                    await getMentionSearchResultsUsing(
                                        context?.query,
                                        char,
                                    )
                                const base = await originalItems(context)
                                return normalizeResults(
                                    asyncResults,
                                    char,
                                    base,
                                    context?.query,
                                )
                            } catch {}
                        }
                        return await originalItems(context)
                    },
                }
            } else {
                const preservedExtraAttributes =
                    suggestionConfig?.extraAttributes
                const searchPrompt = suggestionConfig?.searchPrompt ?? null
                const searchingMessage =
                    suggestionConfig?.searchingMessage ?? null
                suggestion = {
                    ...getMentionSuggestion({
                        items: async ({ query }) => {
                            // Check if baseItems has content (handles both arrays and objects from PHP)
                            const hasBaseItems = Array.isArray(baseItems)
                                ? baseItems.length > 0
                                : baseItems &&
                                  typeof baseItems === 'object' &&
                                  Object.keys(baseItems).length > 0

                            // If no base items and no query, show search prompt instead of searching
                            if (!hasBaseItems && !query) {
                                return []
                            }

                            if (
                                typeof getMentionSearchResultsUsing ===
                                'function'
                            ) {
                                try {
                                    const asyncResults =
                                        await getMentionSearchResultsUsing(
                                            query,
                                            char,
                                        )
                                    return normalizeResults(
                                        asyncResults,
                                        char,
                                        baseItems,
                                        query,
                                    )
                                } catch {}
                            }
                            const base = baseItems
                            if (!query) return base
                            const searchQuery = String(query).toLowerCase()
                            return (base ?? []).filter((item) => {
                                const label =
                                    typeof item === 'string'
                                        ? item
                                        : (item?.label ?? item?.name ?? '')
                                return String(label)
                                    .toLowerCase()
                                    .includes(searchQuery)
                            })
                        },
                        noOptionsMessage,
                        noSearchResultsMessage,
                        searchPrompt,
                        searchingMessage,
                        isSearchable,
                    }),
                    char,
                    ...(preservedExtraAttributes
                        ? { extraAttributes: preservedExtraAttributes }
                        : {}),
                }
            }

            return getSuggestionOptions({
                editor: this.editor,
                overrideSuggestionOptions: suggestion,
                extensionName: this.name,
            })
        })

        this.storage.getSuggestionFromChar = (char) => {
            const suggestion = this.storage.suggestions.find(
                (item) => item.char === char,
            )
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
