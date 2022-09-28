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

// https://github.com/laravel/framework/blob/5299c22321c0f1ea8ff770b84a6c6469c4d6edec/src/Illuminate/Translation/MessageSelector.php#L15
window.pluralize = function (text, number, variables) {
    function extract(segments, number) {
        for (const part of segments) {
            const line = extractFromString(part, number)

            if (line !== null) {
                return line
            }
        }
    }

    function extractFromString(part, number) {
        const matches = part.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s)

        if (matches === null || matches.length !== 3) {
            return null
        }

        const condition = matches[1]

        const value = matches[2]

        if (condition.includes(',')) {
            const [from, to] = condition.split(',', 2)

            if (to === '*' && number >= from) {
                return value
            } else if (from === '*' && number <= to) {
                return value
            } else if (number >= from && number <= to) {
                return value
            }
        }

        return condition == number ? value : null
    }

    function ucfirst(string) {
        return (
            string.toString().charAt(0).toUpperCase() +
            string.toString().slice(1)
        )
    }

    function replace(line, replace) {
        if (replace.length === 0) {
            return line
        }

        const shouldReplace = {}

        for (let [key, value] of Object.entries(replace)) {
            shouldReplace[':' + ucfirst(key ?? '')] = ucfirst(value ?? '')
            shouldReplace[':' + key.toUpperCase()] = value
                .toString()
                .toUpperCase()
            shouldReplace[':' + key] = value
        }

        Object.entries(shouldReplace).forEach(([key, value]) => {
            line = line.replaceAll(key, value)
        })

        return line
    }

    function stripConditions(segments) {
        return segments.map((part) =>
            part.replace(/^[\{\[]([^\[\]\{\}]*)[\}\]]/, ''),
        )
    }

    let segments = text.split('|')

    const value = extract(segments, number)

    if (value !== null && value !== undefined) {
        return replace(value.trim(), variables)
    }

    segments = stripConditions(segments)

    return replace(
        segments.length > 1 && number > 1 ? segments[1] : segments[0],
        variables,
    )
}

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
