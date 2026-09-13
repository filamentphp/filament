/** Values passed between PHP and JavaScript must be JSON-serializable. */
export type JsonValue =
    | null
    | boolean
    | number
    | string
    | readonly JsonValue[]
    | { readonly [key: string]: JsonValue }

export type Snapshot<T> = T extends object
    ? { readonly [Key in keyof T]: Snapshot<T[Key]> }
    : T

export interface JsWidgetProps<Config = Record<string, unknown>> {
    readonly config: Snapshot<Config>
}

/** Common proxy methods. The runtime object is Livewire's unmodified `$wire`. */
export interface JsWidgetLivewire {
    $call<Result = unknown>(
        method: string,
        ...parameters: JsonValue[]
    ): Promise<Result>
    $get<Result = unknown>(path: string, reactive?: boolean): Result
    $set(path: string, value: JsonValue, live?: boolean): Promise<unknown>
}

export interface JsWidgetUtilities<Methods = Record<never, never>> {
    readonly $wire: JsWidgetLivewire & Methods
}

export interface JsWidgetRendererContext<
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> {
    host: HTMLElement
    props: JsWidgetProps<Config>
    utilities: JsWidgetUtilities<Methods>
}

export interface JsWidgetRendererInstance<Config = Record<string, unknown>> {
    /** Calls may overlap. Cancel or order asynchronous work within the renderer. */
    update(props: JsWidgetProps<Config>): void | Promise<void>
    /** Stop DOM work immediately. Remounting does not await cleanup. */
    destroy(): void | Promise<void>
}

export type JsWidgetRenderer<
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> = (
    context: JsWidgetRendererContext<Config, Methods>,
) =>
    | JsWidgetRendererInstance<Config>
    | Promise<JsWidgetRendererInstance<Config>>
