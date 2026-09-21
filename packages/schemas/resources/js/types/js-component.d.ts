import type {
    JsonValue,
    Snapshot,
    JsRendererLivewire,
    JsRendererContext,
    JsRendererInstance,
} from '../../../../support/resources/js/types/js-renderer'

export type {
    JsonValue,
    Snapshot,
} from '../../../../support/resources/js/types/js-renderer'

export interface JsComponentProps<Configuration = Record<string, unknown>> {
    readonly configuration: Snapshot<Configuration>
    readonly id: string | null
}

/** Common proxy methods. The runtime object is Livewire's unmodified `$wire`. */
export interface JsComponentLivewire extends JsRendererLivewire {}

export interface JsComponentUtilities {
    readonly $wire: JsComponentLivewire
    $callSchemaComponentMethod<Result = unknown>(
        method: string,
        argumentsObject?: Record<string, JsonValue>,
    ): Promise<Result | null>
    /** Read when needed, rather than destructuring this getter during mounting. */
    readonly $state: unknown
    readonly $statePath: string | null
    $get<Result = unknown>(
        path: string,
        isAbsolute?: boolean,
    ): Snapshot<Result> | undefined
    $set(
        path: string,
        value: JsonValue,
        isAbsolute?: boolean,
        isLive?: boolean,
    ): Promise<unknown>
}

export interface JsComponentRendererContext<
    Configuration = Record<string, unknown>,
    Methods = Record<never, never>,
> extends JsRendererContext<
    JsComponentProps<Configuration>,
    JsComponentUtilities & Methods
> {}

export interface JsComponentRendererInstance<
    Configuration = Record<string, unknown>,
> extends JsRendererInstance<JsComponentProps<Configuration>> {}

export type JsComponentRenderer<
    Configuration = Record<string, unknown>,
    Methods = Record<never, never>,
> = (
    context: JsComponentRendererContext<Configuration, Methods>,
) =>
    | JsComponentRendererInstance<Configuration>
    | Promise<JsComponentRendererInstance<Configuration>>
