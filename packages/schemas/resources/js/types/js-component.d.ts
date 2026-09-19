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

export interface JsComponentProps<Config = Record<string, unknown>> {
    readonly config: Snapshot<Config>
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
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> extends JsRendererContext<
    JsComponentProps<Config>,
    JsComponentUtilities & Methods
> {}

export interface JsComponentRendererInstance<
    Config = Record<string, unknown>,
> extends JsRendererInstance<JsComponentProps<Config>> {}

export type JsComponentRenderer<
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> = (
    context: JsComponentRendererContext<Config, Methods>,
) =>
    | JsComponentRendererInstance<Config>
    | Promise<JsComponentRendererInstance<Config>>
