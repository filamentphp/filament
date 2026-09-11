import type { JsFieldRenderer } from '../../packages/forms/types/js-field'

interface Value {
    title: string
    tags: string[]
}
interface Config {
    price: number
}

interface Methods {
    $search(argumentsObject: { query: string }): Promise<string | null>
}

const renderer: JsFieldRenderer<Value, Config, Methods> = ({
    host,
    props,
    utilities,
}) => {
    host.setAttribute('aria-describedby', props.ariaDescribedBy)
    props.onChange({ title: 'Valid', tags: [] })
    props.onChange({ ...props.value, title: 'Updated' })
    utilities.$set('tags', props.value.tags)
    props.config.price.toFixed(2)
    utilities.$get<string>('caption')?.toUpperCase()
    utilities.$set('caption', 'Changed', false, true)
    utilities.$search({ query: 'café' }).then((result) => result?.toUpperCase())
    // @ts-expect-error Named methods use the plugin's declared argument types.
    utilities.$search({ query: 17 })
    // @ts-expect-error Unknown aliases are not implicitly callable.
    utilities.$unknownMethod()
    utilities
        .$callSchemaComponentMethod<string>('search', { query: 'café' })
        .then((result) => result?.toUpperCase())
    utilities.$wire
        .$call<number>('recalculate', 3)
        .then((result) => result.toFixed())
    utilities.$wire.$get<string>('data.caption').toUpperCase()
    // @ts-expect-error Component arguments must be named and serializable.
    utilities.$callSchemaComponentMethod('search', ['café'])
    // @ts-expect-error Livewire arguments must be serializable.
    utilities.$wire.$call('recalculate', () => 3)
    // @ts-expect-error The field's value shape must be preserved.
    props.onChange('invalid')
    // @ts-expect-error Incoming state is immutable.
    props.value.tags.push('invalid')
    // @ts-expect-error Configuration is immutable.
    props.config.price = 12
    // @ts-expect-error The utility is a read-only getter.
    utilities.$state = { title: '', tags: [] }
    // @ts-expect-error Schema writes must be serializable.
    utilities.$set('caption', () => 'invalid')
    return {
        update(next) {
            next.value.title.toUpperCase()
            // @ts-expect-error Updates deliberately do not supply callbacks.
            next.onChange(next.value)
        },
        async destroy() {},
    }
}

// @ts-expect-error A renderer must return both lifecycle methods.
const invalid: JsFieldRenderer = () => ({ update() {} })

const asynchronous: JsFieldRenderer<string> = async () => ({
    update() {},
    destroy() {},
})
void [renderer, invalid, asynchronous]
