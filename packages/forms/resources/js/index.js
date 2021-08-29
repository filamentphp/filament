import DateTimePickerFormComponentAlpinePlugin from './components/date-time-picker'
import FileUploadFormComponentAlpinePlugin from './components/file-upload'
import MarkdownEditorFormComponentAlpinePlugin from './components/markdown-editor'
import MultiSelectFormComponentAlpinePlugin from './components/multi-select'
import RichEditorFormComponentAlpinePlugin from './components/rich-editor'
import SelectFormComponentAlpinePlugin from './components/select'
import TagsInputFormComponentAlpinePlugin from './components/tags-input'
import TextInputFormComponentAlpinePlugin from './components/text-input'

export default (Alpine) => {
    Alpine.plugin(DateTimePickerFormComponentAlpinePlugin)
    Alpine.plugin(FileUploadFormComponentAlpinePlugin)
    Alpine.plugin(MarkdownEditorFormComponentAlpinePlugin)
    Alpine.plugin(MultiSelectFormComponentAlpinePlugin)
    Alpine.plugin(RichEditorFormComponentAlpinePlugin)
    Alpine.plugin(SelectFormComponentAlpinePlugin)
    Alpine.plugin(TagsInputFormComponentAlpinePlugin)
    Alpine.plugin(TextInputFormComponentAlpinePlugin)
}
