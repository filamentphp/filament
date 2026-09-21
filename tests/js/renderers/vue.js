import { createApp, h, shallowRef } from 'vue'
import Field from './Field.vue'
import './field.css'

export default function mount({ host, props: initialProps, utilities }) {
    const props = shallowRef({ ...initialProps, utilities })
    const application = createApp({ setup: () => () => h(Field, props.value) })
    application.mount(host)
    return {
        update: (next) => {
            props.value = { ...initialProps, ...next, utilities }
        },
        destroy: () => application.unmount(),
    }
}
