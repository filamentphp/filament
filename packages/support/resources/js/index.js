import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import Focus from '@alpinejs/focus'
import Sortable from './sortable'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Focus)
    window.Alpine.plugin(Sortable)
    window.Alpine.plugin(AlpineFloatingUI)
})

// https://github.com/laravel/framework/blob/5299c22321c0f1ea8ff770b84a6c6469c4d6edec/src/Illuminate/Translation/MessageSelector.php#L15
const pluralize = function (text, number, variables) {
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

window.pluralize = pluralize
