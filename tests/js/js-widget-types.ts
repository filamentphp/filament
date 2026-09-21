import type { JsWidgetRenderer } from '../../packages/widgets/resources/js/types/js-widget'

const renderer: JsWidgetRenderer<
    { total: number; filters: { startDate: string | null } },
    { refreshTotal(): Promise<void> }
> = ({ host, props, utilities }) => {
    const total: number = props.configuration.total
    // @ts-expect-error PHP props are snapshots, not writable Livewire state.
    props.configuration.filters.startDate = '2026-01-01'
    // @ts-expect-error Widgets are not form fields.
    props.onChange(total)
    utilities.$wire.refreshTotal()
    utilities.$wire.$set('period', 'month')
    return {
        update: ({ configuration }) => {
            host.textContent = String(configuration.total)
        },
        destroy: () => host.replaceChildren(),
    }
}

void renderer
