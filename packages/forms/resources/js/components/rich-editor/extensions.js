import Blockquote from '@tiptap/extension-blockquote'
import Bold from '@tiptap/extension-bold'
import BulletList from '@tiptap/extension-bullet-list'
import Code from '@tiptap/extension-code'
import CodeBlock from '@tiptap/extension-code-block'
import Document from '@tiptap/extension-document'
import Heading from '@tiptap/extension-heading'
import History from '@tiptap/extension-history'
import Italic from '@tiptap/extension-italic'
import Image from './extension-image.js'
import Link from '@tiptap/extension-link'
import ListItem from '@tiptap/extension-list-item'
import LocalFiles from './extension-local-files.js'
import OrderedList from '@tiptap/extension-ordered-list'
import Paragraph from '@tiptap/extension-paragraph'
import Strike from '@tiptap/extension-strike'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import Text from '@tiptap/extension-text'
import Underline from '@tiptap/extension-underline'

export default ({ key, statePath, uploadingFileMessage, $wire }) => [
    Blockquote,
    Bold,
    BulletList,
    Code,
    CodeBlock,
    Document,
    Heading,
    History,
    Italic,
    Image,
    Link.configure({
        autolink: true,
        openOnClick: false,
    }),
    ListItem,
    LocalFiles.configure({
        key,
        statePath,
        uploadingMessage: uploadingFileMessage,
        $wire: () => $wire,
    }),
    OrderedList,
    Paragraph,
    Strike,
    Subscript,
    Superscript,
    Text,
    Underline,
]
