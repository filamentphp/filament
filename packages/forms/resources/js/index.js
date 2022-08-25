import ColorPickerFormComponentAlpinePlugin from './components/color-picker'
import DateTimePickerFormComponentAlpinePlugin from './components/date-time-picker'
import FileUploadFormComponentAlpinePlugin from './components/file-upload'
import KeyValueFormComponentAlpinePlugin from './components/key-value'
import MarkdownEditorFormComponentAlpinePlugin from './components/markdown-editor'
import RichEditorFormComponentAlpinePlugin from './components/rich-editor'
import SelectFormComponentAlpinePlugin from './components/select'
import TagsInputFormComponentAlpinePlugin from './components/tags-input'
import TextInputFormComponentAlpinePlugin from './components/text-input'
import TextareaFormComponentAlpinePlugin from './components/textarea'
import SortableAlpinePlugin from './sortable'
import AlpineFloatingUI from '@awcodes/alpine-floating-ui'

export default (Alpine) => {
    Alpine.plugin(ColorPickerFormComponentAlpinePlugin)
    Alpine.plugin(DateTimePickerFormComponentAlpinePlugin)
    Alpine.plugin(FileUploadFormComponentAlpinePlugin)
    Alpine.plugin(KeyValueFormComponentAlpinePlugin)
    Alpine.plugin(MarkdownEditorFormComponentAlpinePlugin)
    Alpine.plugin(RichEditorFormComponentAlpinePlugin)
    Alpine.plugin(SelectFormComponentAlpinePlugin)
    Alpine.plugin(TagsInputFormComponentAlpinePlugin)
    Alpine.plugin(TextInputFormComponentAlpinePlugin)
    Alpine.plugin(TextareaFormComponentAlpinePlugin)
    Alpine.plugin(SortableAlpinePlugin)
    Alpine.plugin(AlpineFloatingUI)
}

export {
    ColorPickerFormComponentAlpinePlugin,
    DateTimePickerFormComponentAlpinePlugin,
    FileUploadFormComponentAlpinePlugin,
    KeyValueFormComponentAlpinePlugin,
    MarkdownEditorFormComponentAlpinePlugin,
    RichEditorFormComponentAlpinePlugin,
    SelectFormComponentAlpinePlugin,
    TagsInputFormComponentAlpinePlugin,
    TextInputFormComponentAlpinePlugin,
    TextareaFormComponentAlpinePlugin,
    SortableAlpinePlugin,
    AlpineFloatingUI,
}
