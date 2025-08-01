import { Dropcursor, Gapcursor, UndoRedo } from '@tiptap/extensions'
import Blockquote from '@tiptap/extension-blockquote'
import Bold from '@tiptap/extension-bold'
import Code from '@tiptap/extension-code'
import CodeBlock from '@tiptap/extension-code-block'
import CustomBlock from './extension-custom-block.js'
import {
    Details,
    DetailsSummary,
    DetailsContent,
} from '@tiptap/extension-details'
import Document from '@tiptap/extension-document'
import HardBreak from '@tiptap/extension-hard-break'
import Heading from '@tiptap/extension-heading'
import Highlight from '@tiptap/extension-highlight'
import HorizontalRule from '@tiptap/extension-horizontal-rule'
import Italic from '@tiptap/extension-italic'
import Image from './extension-image.js'
import Lead from './extension-lead.js'
import Link from '@tiptap/extension-link'
import { BulletList, ListItem, OrderedList } from '@tiptap/extension-list'
import LocalFiles from './extension-local-files.js'
import MergeTag from './extension-merge-tag.js'
import Paragraph from '@tiptap/extension-paragraph'
import Placeholder from '@tiptap/extension-placeholder'
import Small from './extension-small.js'
import Strike from '@tiptap/extension-strike'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import { TableKit } from '@tiptap/extension-table'
import Text from '@tiptap/extension-text'
import TextAlign from '@tiptap/extension-text-align'
import Underline from '@tiptap/extension-underline'

import getMergeTagSuggestion from './merge-tag-suggestion.js'

export default async ({
    customExtensionUrls,
    deleteCustomBlockButtonIconHtml,
    editCustomBlockButtonIconHtml,
    editCustomBlockUsing,
    insertCustomBlockUsing,
    key,
    mergeTags,
    noMergeTagSearchResultsMessage,
    placeholder,
    statePath,
    uploadingFileMessage,
    $wire,
}) => [
    Blockquote,
    Bold,
    BulletList,
    Code,
    CodeBlock,
    CustomBlock.configure({
        deleteCustomBlockButtonIconHtml,
        editCustomBlockButtonIconHtml,
        editCustomBlockUsing,
        insertCustomBlockUsing,
    }),
    Details,
    DetailsSummary,
    DetailsContent,
    Document,
    Dropcursor,
    Gapcursor,
    HardBreak,
    Heading,
    Highlight,
    HorizontalRule,
    Italic,
    Image.configure({
        inline: true,
    }),
    Lead,
    Link.configure({
        autolink: true,
        openOnClick: false,
    }),
    ListItem,
    LocalFiles.configure({
        get$WireUsing: () => $wire,
        key,
        statePath,
        uploadingMessage: uploadingFileMessage,
    }),
    ...(mergeTags.length
        ? [
              MergeTag.configure({
                  deleteTriggerWithBackspace: true,
                  suggestion: getMergeTagSuggestion({
                      mergeTags,
                      noMergeTagSearchResultsMessage,
                  }),
              }),
          ]
        : []),
    OrderedList,
    Paragraph,
    Placeholder.configure({
        placeholder,
    }),
    Small,
    Strike,
    Subscript,
    Superscript,
    TableKit.configure({
        table: {
            resizable: true,
        },
    }),
    Text,
    TextAlign.configure({
        types: ['heading', 'paragraph'],
        alignments: ['start', 'center', 'end', 'justify'],
        defaultAlignment: 'start',
    }),
    Underline,
    UndoRedo,
    ...(
        await Promise.all(
            customExtensionUrls.map(async (url) => {
                const absoluteUrlRegExp = new RegExp('^(?:[a-z+]+:)?//', 'i')

                if (!absoluteUrlRegExp.test(url)) {
                    url = new URL(url, document.baseURI).href
                }

                return (await import(url)).default
            }),
        )
    ).flat(),
]
