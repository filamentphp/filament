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

export interface JsWidgetProps<Config = Record<string, unknown>> {
    readonly config: Snapshot<Config>
}

/** Common proxy methods. The runtime object is Livewire's unmodified `$wire`. */
export interface JsWidgetLivewire extends JsRendererLivewire {}

export interface JsWidgetUtilities<Methods = Record<never, never>> {
    readonly $wire: JsWidgetLivewire & Methods
}

export interface JsWidgetRendererContext<
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> extends JsRendererContext<
    JsWidgetProps<Config>,
    JsWidgetUtilities<Methods>
> {}

export interface JsWidgetRendererInstance<
    Config = Record<string, unknown>,
> extends JsRendererInstance<JsWidgetProps<Config>> {}

export type JsWidgetRenderer<
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> = (
    context: JsWidgetRendererContext<Config, Methods>,
) =>
    | JsWidgetRendererInstance<Config>
    | Promise<JsWidgetRendererInstance<Config>>
