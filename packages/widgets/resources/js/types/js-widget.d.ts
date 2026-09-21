import type {
    Snapshot,
    JsRendererLivewire,
    JsRendererContext,
    JsRendererInstance,
} from '../../../../support/resources/js/types/js-renderer'

export type {
    JsonValue,
    Snapshot,
} from '../../../../support/resources/js/types/js-renderer'

export interface JsWidgetProps<Configuration = Record<string, unknown>> {
    readonly configuration: Snapshot<Configuration>
}

/** Common proxy methods. The runtime object is Livewire's unmodified `$wire`. */
export interface JsWidgetLivewire extends JsRendererLivewire {}

export interface JsWidgetUtilities<Methods = Record<never, never>> {
    readonly $wire: JsWidgetLivewire & Methods
}

export interface JsWidgetRendererContext<
    Configuration = Record<string, unknown>,
    Methods = Record<never, never>,
> extends JsRendererContext<
    JsWidgetProps<Configuration>,
    JsWidgetUtilities<Methods>
> {}

export interface JsWidgetRendererInstance<
    Configuration = Record<string, unknown>,
> extends JsRendererInstance<JsWidgetProps<Configuration>> {}

export type JsWidgetRenderer<
    Configuration = Record<string, unknown>,
    Methods = Record<never, never>,
> = (
    context: JsWidgetRendererContext<Configuration, Methods>,
) =>
    | JsWidgetRendererInstance<Configuration>
    | Promise<JsWidgetRendererInstance<Configuration>>
