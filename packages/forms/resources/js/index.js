import './sortable'
import ColorPickerFormComponentAlpinePlugin from './components/color-picker'
import DateTimePickerFormComponentAlpinePlugin from './components/date-time-picker'
import FileUploadFormComponentAlpinePlugin from './components/file-upload'
import KeyValueFormComponentAlpinePlugin from './components/key-value'
import MarkdownEditorFormComponentAlpinePlugin from './components/markdown-editor'
import RichEditorFormComponentAlpinePlugin from './components/rich-editor'
import SelectFormComponentAlpinePlugin from './components/select'
import TagsInputFormComponentAlpinePlugin from './components/tags-input'
import TextareaFormComponentAlpinePlugin from './components/textarea'
import TextInputFormComponentAlpinePlugin from './components/text-input'

export default (Alpine) => {
    Alpine.plugin(ColorPickerFormComponentAlpinePlugin)
    Alpine.plugin(DateTimePickerFormComponentAlpinePlugin)
    Alpine.plugin(FileUploadFormComponentAlpinePlugin)
    Alpine.plugin(KeyValueFormComponentAlpinePlugin)
    Alpine.plugin(MarkdownEditorFormComponentAlpinePlugin)
    Alpine.plugin(RichEditorFormComponentAlpinePlugin)
    Alpine.plugin(SelectFormComponentAlpinePlugin)
    Alpine.plugin(TagsInputFormComponentAlpinePlugin)
    Alpine.plugin(TextareaFormComponentAlpinePlugin)
    Alpine.plugin(TextInputFormComponentAlpinePlugin)
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
    TextareaFormComponentAlpinePlugin,
    TextInputFormComponentAlpinePlugin,
}
