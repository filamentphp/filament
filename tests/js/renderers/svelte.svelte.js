import { mount, unmount } from 'svelte'
import Field from './Field.svelte'
import './field.css'

export default function mountField({ host, props: initialProps, utilities }) {
    const props = $state({ ...initialProps, utilities })
    const component = mount(Field, { target: host, props })
    return {
        update: (next) => Object.assign(props, next),
        destroy: () => unmount(component),
    }
}
