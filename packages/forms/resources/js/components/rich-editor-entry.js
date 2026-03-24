/**
 * Rich Editor entry point.
 *
 * Exposes the bundled TipTap/ProseMirror modules on window.Filament.TipTap
 * so that custom extensions loaded via RichContentPlugin::getTipTapJsExtensions()
 * can reference the same ProseMirror instance instead of bundling their own.
 *
 * Custom extension builds should mark @tiptap/* as external and resolve
 * named imports from window.Filament.TipTap at runtime:
 *
 *   const { Node, mergeAttributes } = window.Filament.TipTap['core']
 *   const { Plugin, PluginKey } = window.Filament.TipTap['pm/state']
 */

import * as TipTapCore from '@tiptap/core'
import * as TipTapPmState from '@tiptap/pm/state'
import * as TipTapPmView from '@tiptap/pm/view'
import * as TipTapPmModel from '@tiptap/pm/model'

window.Filament = window.Filament || {}
window.Filament.TipTap = {
    core: TipTapCore,
    'pm/state': TipTapPmState,
    'pm/view': TipTapPmView,
    'pm/model': TipTapPmModel,
}

export { default } from './rich-editor.js'
