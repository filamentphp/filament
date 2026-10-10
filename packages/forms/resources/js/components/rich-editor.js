import * as TipTapCore from '@tiptap/core'
import * as TipTapPmState from '@tiptap/pm/state'
import * as TipTapPmView from '@tiptap/pm/view'
import * as TipTapPmModel from '@tiptap/pm/model'
import getExtensions from './rich-editor/extensions'
import { BubbleMenuPlugin } from '@tiptap/extension-bubble-menu'

const { Editor } = TipTapCore
const { Selection } = TipTapPmState

// Expose the bundled TipTap/ProseMirror modules so custom extensions loaded
// via `RichContentPlugin::getTipTapJsExtensions()` can share the same
// ProseMirror instance (required for `instanceof` checks across bundles).
window.FilamentRichEditor = window.FilamentRichEditor || {}
window.FilamentRichEditor.tiptap = {
    core: TipTapCore,
    pmState: TipTapPmState,
    pmView: TipTapPmView,
    pmModel: TipTapPmModel,
}

export default function richEditorFormComponent({
    acceptedFileTypes,
    acceptedFileTypesValidationMessage,
    activePanel,
    canAttachFiles,
    deleteCustomBlockButtonIconHtml,
    deleteCustomBlockButtonLabel,
    editCustomBlockButtonIconHtml,
    editCustomBlockButtonLabel,
    extensions,
    floatingToolbars,
    hasResizableImages,
    hasMinimalCustomBlockControls = false,
    hasStickyToolbar = false,
    isDisabled,
    isLiveDebounced,
    isLiveOnBlur,
    key,
    label,
    linkProtocols,
    liveDebounce,
    livewireId,
    maxFileSize,
    maxFileSizeValidationMessage,
    mergeTags,
    mentions,
    getMentionSearchResultsUsing,
    getMentionLabelsUsing,
    noMergeTagSearchResultsMessage,
    placeholder,
    state,
    statePath,
    textColors,
    uploadingFileMessage,
}) {
    let editor
    let eventListeners = []
    let isDestroyed = false
    let toolbarResizeObserver
    let modalResizeObserver
    let modalMutationObserver
    let toolbarMutationObserver
    const toolbarTools = new Map()
    const floatingToolbarElements = new Map()

    return {
        state,

        activePanel,

        customBlockSearch: '',

        editorSelection: { type: 'text', anchor: 1, head: 1 },

        isUploadingFile: false,

        fileValidationMessage: null,

        shouldUpdateState: true,

        editorUpdatedAt: Date.now(),

        async init() {
            const resolvedExtensions = await getExtensions({
                acceptedFileTypes,
                acceptedFileTypesValidationMessage,
                canAttachFiles,
                customExtensionUrls: extensions,
                deleteCustomBlockButtonIconHtml,
                deleteCustomBlockButtonLabel,
                editCustomBlockButtonIconHtml,
                editCustomBlockButtonLabel,
                editCustomBlockUsing: (id, config) =>
                    this.$wire.mountAction(
                        'customBlock',
                        {
                            editorSelection: this.editorSelection,
                            id,
                            config,
                            mode: 'edit',
                        },
                        { schemaComponent: key },
                    ),
                floatingToolbars,
                getCustomBlockPreviewsUsing: (customBlocks) =>
                    this.$wire.callSchemaComponentMethod(
                        key,
                        'getCustomBlockPreviewsForJs',
                        { customBlocks },
                    ),
                hasResizableImages,
                hasMinimalCustomBlockControls,
                insertCustomBlockUsing: (id, dragPosition = null) =>
                    this.$wire.mountAction(
                        'customBlock',
                        { id, dragPosition, mode: 'insert' },
                        { schemaComponent: key },
                    ),
                key,
                linkProtocols,
                maxFileSize,
                maxFileSizeValidationMessage,
                mergeTags,
                mentions,
                getMentionSearchResultsUsing,
                getMentionLabelsUsing,
                noMergeTagSearchResultsMessage,
                placeholder,
                statePath,
                textColors,
                uploadingFileMessage,
                $wire: this.$wire,
            })

            if (isDestroyed) {
                return
            }

            if (this.$refs.toolbar) {
                toolbarResizeObserver = new ResizeObserver(([entry]) => {
                    if (isDestroyed) return

                    if (hasStickyToolbar) {
                        this.$el.style.setProperty(
                            '--fi-fo-rich-editor-toolbar-height',
                            `${entry.borderBoxSize[0].blockSize}px`,
                        )
                    }

                    this.syncToolbarTools()
                })
                toolbarResizeObserver.observe(this.$refs.toolbar, {
                    box: 'border-box',
                })
            }

            const modal = this.$el.closest('.fi-modal')
            const hasStickyPanels = !!this.$el.querySelector(
                '.fi-fo-rich-editor-sticky-panels',
            )

            if (modal && (hasStickyToolbar || hasStickyPanels)) {
                const modalWindow = modal.querySelector(
                    ':scope > .fi-modal-window-ctn > .fi-modal-window',
                )
                let modalHeader
                let modalFooter
                let modalViewport

                const updateModalMeasurements = () => {
                    const nextModalHeader = modal.matches(
                        '.fi-modal-has-sticky-header',
                    )
                        ? modalWindow.querySelector(':scope > .fi-modal-header')
                        : null
                    const nextModalFooter =
                        hasStickyPanels &&
                        modal.matches('.fi-modal-has-sticky-footer')
                            ? modalWindow.querySelector(
                                  ':scope > .fi-modal-footer',
                              )
                            : null
                    const nextModalViewport = hasStickyPanels
                        ? modalWindow.parentElement
                        : null

                    for (const [previous, next, property, box] of [
                        [
                            modalHeader,
                            nextModalHeader,
                            '--fi-fo-rich-editor-modal-header-height',
                            'border-box',
                        ],
                        [
                            modalFooter,
                            nextModalFooter,
                            '--fi-fo-rich-editor-modal-footer-height',
                            'border-box',
                        ],
                        [
                            modalViewport,
                            nextModalViewport,
                            '--fi-fo-rich-editor-modal-viewport-height',
                            'content-box',
                        ],
                    ]) {
                        if (previous === next) {
                            continue
                        }

                        if (previous) {
                            modalResizeObserver.unobserve(previous)
                        }

                        this.$el.style.removeProperty(property)

                        if (next) {
                            modalResizeObserver.observe(next, { box })
                        }
                    }

                    modalHeader = nextModalHeader
                    modalFooter = nextModalFooter
                    modalViewport = nextModalViewport
                }

                modalResizeObserver = new ResizeObserver((entries) => {
                    for (const entry of entries) {
                        const property =
                            entry.target === modalHeader
                                ? '--fi-fo-rich-editor-modal-header-height'
                                : entry.target === modalFooter
                                  ? '--fi-fo-rich-editor-modal-footer-height'
                                  : '--fi-fo-rich-editor-modal-viewport-height'
                        const height =
                            entry.target === modalViewport
                                ? entry.contentBoxSize[0].blockSize
                                : entry.borderBoxSize[0].blockSize

                        this.$el.style.setProperty(property, `${height}px`)
                    }
                })

                updateModalMeasurements()

                modalMutationObserver = new MutationObserver(
                    updateModalMeasurements,
                )
                modalMutationObserver.observe(modal, {
                    attributes: true,
                    attributeFilter: ['class'],
                })
                modalMutationObserver.observe(modalWindow, {
                    childList: true,
                })
            }

            editor = new Editor({
                editable: !isDisabled,
                element: this.$refs.editor,
                editorProps: {
                    attributes: {
                        ...(label ? { 'aria-label': label } : {}),
                        'aria-multiline': 'true',
                        'data-testid': 'rich-editor-content',
                    },
                },
                extensions: resolvedExtensions,
                content: this.state,
            })

            Object.keys(floatingToolbars).forEach((key) => {
                const element = this.$refs[`floatingToolbar::${key}`]

                if (!element) {
                    console.warn(`Floating toolbar [${key}] not found.`)

                    return
                }

                floatingToolbarElements.set(key, element)

                editor.registerPlugin(
                    BubbleMenuPlugin({
                        editor,
                        element,
                        pluginKey: `floatingToolbar::${key}`,
                        getReferencedVirtualElement:
                            key === 'grid'
                                ? () => {
                                      const { node } = editor.view.domAtPos(
                                          editor.state.selection.from,
                                      )

                                      return (
                                          node.nodeType === Node.TEXT_NODE
                                              ? node.parentElement
                                              : node
                                      )?.closest('.grid-layout')
                                  }
                                : undefined,
                        shouldShow: () =>
                            !isDestroyed &&
                            this.isFloatingToolbarEligible(key) &&
                            (editor.isFocused ||
                                element.contains(document.activeElement)),
                        options: {
                            placement: key === 'grid' ? 'top-end' : 'bottom',
                            offset: 15,
                            ...(key === 'grid'
                                ? {
                                      flip: {
                                          fallbackPlacements: ['bottom-end'],
                                      },
                                  }
                                : {}),
                        },
                    }),
                )

                // TipTap makes the menu wrapper tabbable; only its tools own focus.
                element.tabIndex = -1
            })

            toolbarMutationObserver = new MutationObserver(() => {
                if (!isDestroyed) this.syncToolbarTools()
            })

            for (const toolbar of [
                this.$refs.toolbar,
                ...floatingToolbarElements.values(),
            ]) {
                if (!toolbar) continue

                toolbarMutationObserver.observe(toolbar, {
                    attributes: true,
                    attributeFilter: [
                        'disabled',
                        'aria-disabled',
                        'hidden',
                        'style',
                        'class',
                    ],
                    childList: true,
                    subtree: true,
                })
            }

            this.syncToolbarTools()

            editor.on('create', () => {
                this.editorUpdatedAt = Date.now()
            })

            const debouncedCommit = Alpine.debounce(() => {
                if (!isDestroyed) {
                    this.$wire.commit()
                }
            }, liveDebounce ?? 300)

            editor.on('update', ({ editor }) =>
                this.$nextTick(() => {
                    if (isDestroyed) return

                    this.editorUpdatedAt = Date.now()

                    this.state = editor.getJSON()

                    this.shouldUpdateState = false

                    this.fileValidationMessage = null

                    if (isLiveDebounced) {
                        debouncedCommit()
                    }
                }),
            )

            editor.on('selectionUpdate', ({ transaction }) => {
                if (isDestroyed) return

                this.editorUpdatedAt = Date.now()
                this.editorSelection = transaction.selection.toJSON()
            })

            editor.on('transaction', () => {
                if (isDestroyed) return

                this.editorUpdatedAt = Date.now()
            })

            if (isLiveOnBlur) {
                editor.on('blur', () => {
                    if (!isDestroyed) {
                        this.$wire.commit()
                    }
                })
            }

            this.$watch('state', () => {
                if (isDestroyed) return

                if (!this.shouldUpdateState) {
                    this.shouldUpdateState = true

                    return
                }

                editor.commands.setContent(this.state)
            })

            const runCommandsHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.runEditorCommands(event.detail)
            }
            window.addEventListener(
                'run-rich-editor-commands',
                runCommandsHandler,
            )
            eventListeners.push([
                'run-rich-editor-commands',
                runCommandsHandler,
            ])

            const uploadingFileHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = true
                this.fileValidationMessage = null

                event.stopPropagation()
            }
            window.addEventListener(
                'rich-editor-uploading-file',
                uploadingFileHandler,
            )
            eventListeners.push([
                'rich-editor-uploading-file',
                uploadingFileHandler,
            ])

            const uploadedFileHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = false

                event.stopPropagation()
            }
            window.addEventListener(
                'rich-editor-uploaded-file',
                uploadedFileHandler,
            )
            eventListeners.push([
                'rich-editor-uploaded-file',
                uploadedFileHandler,
            ])

            const validationMessageHandler = (event) => {
                if (event.detail.livewireId !== livewireId) {
                    return
                }

                if (event.detail.key !== key) {
                    return
                }

                this.isUploadingFile = false
                this.fileValidationMessage = event.detail.validationMessage

                event.stopPropagation()
            }
            window.addEventListener(
                'rich-editor-file-validation-message',
                validationMessageHandler,
            )
            eventListeners.push([
                'rich-editor-file-validation-message',
                validationMessageHandler,
            ])

            window.dispatchEvent(
                new CustomEvent(`schema-component-${livewireId}-${key}-loaded`),
            )
        },

        getEditor() {
            return editor
        },

        $getEditor() {
            return this.getEditor()
        },

        isFloatingToolbarEligible(name) {
            if (!editor?.isEditable || !editor.isActive(name)) return false

            const hasParagraphSelection =
                editor.isActive('paragraph') && !editor.state.selection.empty

            return name === 'paragraph'
                ? hasParagraphSelection
                : !('paragraph' in floatingToolbars && hasParagraphSelection)
        },

        getToolbarTools(toolbar) {
            if (!toolbar) return []

            return Array.from(
                toolbar.querySelectorAll('[data-rich-editor-tool]'),
            ).filter(
                (tool) =>
                    tool.closest('[data-rich-editor-toolbar]') === toolbar &&
                    !tool.closest('[role="menu"]') &&
                    !tool.disabled &&
                    tool.getAttribute('aria-disabled') !== 'true' &&
                    tool.getClientRects().length &&
                    getComputedStyle(tool).visibility !== 'hidden',
            )
        },

        syncToolbarTools() {
            for (const toolbar of [
                this.$refs.toolbar,
                ...floatingToolbarElements.values(),
            ]) {
                if (!toolbar) continue

                const tools = this.getToolbarTools(toolbar)
                const previous = toolbarTools.get(toolbar)
                const tool = tools.includes(previous?.tool)
                    ? previous.tool
                    : tools[Math.min(previous?.index ?? 0, tools.length - 1)]

                toolbar
                    .querySelectorAll('[data-rich-editor-tool]')
                    .forEach((candidate) => {
                        candidate.tabIndex =
                            toolbar === this.$refs.toolbar && candidate === tool
                                ? 0
                                : -1
                    })

                toolbarTools.set(toolbar, {
                    tool,
                    index: tool ? tools.indexOf(tool) : (previous?.index ?? 0),
                })

                if (
                    previous?.tool &&
                    previous.tool !== tool &&
                    (document.activeElement === previous.tool ||
                        (!previous.tool.isConnected &&
                            document.activeElement === document.body))
                ) {
                    if (tool) tool.focus()
                    else if (toolbar.isConnected) this.focusEditor()
                }
            }
        },

        handleToolbarFocusin(event) {
            const toolbar = event.target.closest('[data-rich-editor-toolbar]')
            if (!toolbar) return

            const tools = this.getToolbarTools(toolbar)
            if (!tools.includes(event.target)) return

            toolbarTools.set(toolbar, {
                tool: event.target,
                index: tools.indexOf(event.target),
            })
            this.syncToolbarTools()
        },

        handleToolbarFocusout(event) {
            const toolbar = event.target.closest('[data-rich-editor-toolbar]')
            if (!toolbar || toolbar.contains(event.relatedTarget)) return

            this.$nextTick(() => {
                if (isDestroyed || toolbar.contains(document.activeElement)) {
                    return
                }

                for (const [name, element] of floatingToolbarElements) {
                    if (element === toolbar && !editor.isFocused) {
                        editor.view.dispatch(
                            editor.state.tr.setMeta(
                                `floatingToolbar::${name}`,
                                'hide',
                            ),
                        )
                    }
                }
            })
        },

        handleToolbarKeydown(event) {
            if (isDestroyed || !editor || event.defaultPrevented) return

            const toolbar = event.target.closest('[data-rich-editor-toolbar]')
            const isEditor = event.target === editor.view.dom

            if (
                event.key === 'F10' &&
                event.altKey &&
                !event.ctrlKey &&
                !event.metaKey &&
                !event.shiftKey &&
                (isEditor ||
                    (toolbar && !event.target.closest('[role="menu"]')))
            ) {
                const toolbars = Array.from(floatingToolbarElements)
                    .filter(([name]) => this.isFloatingToolbarEligible(name))
                    .map(([, element]) => element)

                if (
                    this.$refs.toolbar &&
                    this.getToolbarTools(this.$refs.toolbar).length
                ) {
                    toolbars.push(this.$refs.toolbar)
                }

                if (!toolbars.length) return

                for (let offset = 1; offset <= toolbars.length; offset++) {
                    const next =
                        toolbars[
                            (toolbars.indexOf(toolbar) + offset) %
                                toolbars.length
                        ]
                    const name = Array.from(floatingToolbarElements).find(
                        ([, element]) => element === next,
                    )?.[0]

                    if (name) {
                        editor.view.dispatch(
                            editor.state.tr.setMeta(
                                `floatingToolbar::${name}`,
                                'show',
                            ),
                        )
                        editor.view.dispatch(
                            editor.state.tr.setMeta(
                                `floatingToolbar::${name}`,
                                'updatePosition',
                            ),
                        )
                    }

                    this.syncToolbarTools()
                    const tool = toolbarTools.get(next)?.tool

                    if (tool) {
                        event.preventDefault()
                        tool.focus()
                        return
                    }

                    if (name)
                        editor.view.dispatch(
                            editor.state.tr.setMeta(
                                `floatingToolbar::${name}`,
                                'hide',
                            ),
                        )
                }

                return
            }

            if (
                !toolbar ||
                event.target.closest('[role="menu"]') ||
                event.altKey ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey
            )
                return

            const tools = this.getToolbarTools(toolbar)
            const index = tools.indexOf(event.target)
            if (index === -1) return

            if (event.key === 'Escape') {
                event.preventDefault()
                event.stopPropagation()
                this.focusEditor()
                return
            }

            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key))
                return

            event.preventDefault()
            const isRtl = getComputedStyle(toolbar).direction === 'rtl'
            const offset = (event.key === 'ArrowRight') !== isRtl ? 1 : -1
            const nextIndex =
                event.key === 'Home'
                    ? 0
                    : event.key === 'End'
                      ? tools.length - 1
                      : (index + offset + tools.length) % tools.length
            tools[nextIndex].focus()
        },

        handleToolbarMenuKeydown(event) {
            const menu = event.currentTarget
            if (
                isDestroyed ||
                event.defaultPrevented ||
                menu.getAttribute('aria-orientation') !== 'horizontal' ||
                event.target.closest('[role="menu"]') !== menu ||
                event.altKey ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)
            )
                return

            const tools = Alpine.$data(
                menu.closest('.fi-dropdown'),
            ).getMenuItems()
            const index = tools.indexOf(event.target)
            if (index === -1) return

            event.preventDefault()
            event.stopImmediatePropagation()
            const isRtl = getComputedStyle(menu).direction === 'rtl'
            const offset = (event.key === 'ArrowRight') !== isRtl ? 1 : -1
            const nextIndex =
                event.key === 'Home'
                    ? 0
                    : event.key === 'End'
                      ? tools.length - 1
                      : (index + offset + tools.length) % tools.length
            tools[nextIndex].focus()
        },

        focusEditor() {
            editor?.commands.focus(null, { scrollIntoView: false })
        },

        async mountToolAction(
            name,
            actionArguments = {},
            element = document.activeElement,
        ) {
            const originalEditor = editor
            const originalDocument = JSON.stringify(editor.getJSON())
            const selection = editor.state.selection.toJSON()
            const origin = element.closest('[role="menu"]')
                ? Alpine.$data(element.closest('.fi-dropdown')).getTrigger()
                : element

            for (const tool of [element, origin]) {
                tool._tippy?.clearDelayTimeouts()
                tool._tippy?.hide()
            }

            const toolbar = origin.closest('[data-rich-editor-toolbar]')
            const toolName = origin.dataset.richEditorTool
            const isMainToolbar = toolbar === this.$refs.toolbar
            const toolIndex = isMainToolbar
                ? Array.from(
                      toolbar.querySelectorAll('[data-rich-editor-tool]'),
                  ).indexOf(origin)
                : -1

            if (!isMainToolbar) editor.view.focus()

            const registration = {
                id: livewireId,
                createFocusTarget: (signal) => {
                    let hasRunCommands = false
                    window.addEventListener(
                        'run-rich-editor-commands',
                        (event) => {
                            if (
                                event.detail.livewireId === livewireId &&
                                event.detail.key === key
                            ) {
                                hasRunCommands = true
                            }
                        },
                        { signal },
                    )

                    return async () => {
                        const findComponent = () => {
                            const root = Livewire.find(
                                livewireId,
                            )?.$el?.querySelectorAll('[data-rich-editor-key]')
                            const element = Array.from(root ?? []).find(
                                (element) =>
                                    element.dataset.richEditorKey === key &&
                                    element
                                        .closest('[wire\\:id]')
                                        ?.getAttribute('wire:id') ===
                                        livewireId,
                            )
                            return element ? Alpine.$data(element) : null
                        }

                        let component = findComponent()
                        if (!component) return null

                        if (!component.getEditor()) {
                            let hasMovedFocus = false
                            let handleFocusin
                            await new Promise((resolve) => {
                                handleFocusin = (event) => {
                                    if (
                                        event.target === document.body ||
                                        !event.target.isConnected
                                    )
                                        return

                                    hasMovedFocus = true
                                    resolve()
                                }
                                window.addEventListener(
                                    'focusin',
                                    handleFocusin,
                                    { signal },
                                )
                                window.addEventListener(
                                    `schema-component-${livewireId}-${key}-loaded`,
                                    resolve,
                                    { once: true, signal },
                                )
                                signal.addEventListener('abort', resolve, {
                                    once: true,
                                })
                            })
                            window.removeEventListener('focusin', handleFocusin)
                            if (hasMovedFocus) return null
                            component = findComponent()
                        }

                        if (signal.aborted || !component?.getEditor())
                            return null

                        if (
                            component.getEditor() !== originalEditor &&
                            !hasRunCommands &&
                            JSON.stringify(component.getEditor().getJSON()) ===
                                originalDocument
                        ) {
                            component.setEditorSelection(selection)
                        }

                        if (isMainToolbar) {
                            const tools = component.getToolbarTools(
                                component.$refs.toolbar,
                            )
                            const tool = tools.includes(origin)
                                ? origin
                                : component.$refs.toolbar?.querySelectorAll(
                                      '[data-rich-editor-tool]',
                                  )[toolIndex]
                            if (
                                tools.includes(tool) &&
                                tool.dataset.richEditorTool === toolName
                            ) {
                                tool.focus({ preventScroll: true })
                                return
                            }
                        }

                        component.getEditor().view.focus()
                    }
                },
            }

            window.dispatchEvent(
                new CustomEvent('action-modal-focus-target', {
                    detail: registration,
                }),
            )

            try {
                return await this.$wire.mountAction(name, actionArguments, {
                    schemaComponent: key,
                })
            } finally {
                window.dispatchEvent(
                    new CustomEvent('action-modal-focus-target-finished', {
                        detail: registration,
                    }),
                )
            }
        },

        setEditorSelection(selection) {
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

        runEditorCommands({ commands, editorSelection }) {
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

        matchesCustomBlockSearch(labels) {
            const search = this.customBlockSearch.trim().toLocaleLowerCase()

            return (
                !search ||
                labels.some((label) =>
                    label.toLocaleLowerCase().includes(search),
                )
            )
        },

        togglePanel(id = null) {
            if (this.isPanelActive(id)) {
                this.activePanel = null

                return
            }

            this.activePanel = id
        },

        isPanelActive(id = null) {
            if (id === null) {
                return this.activePanel !== null
            }

            return this.activePanel === id
        },

        insertMergeTag(id) {
            editor
                .chain()
                .focus()
                .insertContent([
                    {
                        type: 'mergeTag',
                        attrs: { id },
                    },
                    {
                        type: 'text',
                        text: ' ',
                    },
                ])
                .run()
        },

        destroy() {
            isDestroyed = true
            toolbarResizeObserver?.disconnect()
            modalResizeObserver?.disconnect()
            modalMutationObserver?.disconnect()
            toolbarMutationObserver?.disconnect()
            toolbarTools.clear()
            floatingToolbarElements.clear()

            eventListeners.forEach(([eventName, handler]) => {
                window.removeEventListener(eventName, handler)
            })
            eventListeners = []

            if (editor) {
                editor.destroy()
                editor = null
            }

            this.shouldUpdateState = true
        },
    }
}
