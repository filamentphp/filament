/** Values must be JSON-serializable. */
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

/** Common proxy methods. The runtime object is Livewire's unmodified `$wire`. */
export interface JsRendererLivewire {
    $call<Result = unknown>(
        method: string,
        ...parameters: JsonValue[]
    ): Promise<Result>
    $get<Result = unknown>(path: string, isReactive?: boolean): Result
    $set(path: string, value: JsonValue, isLive?: boolean): Promise<unknown>
}

export interface JsRendererContext<Props, Utilities> {
    host: HTMLElement
    props: Props
    utilities: Utilities
}

export interface JsRendererInstance<Props> {
    /** Calls may overlap. Cancel or order asynchronous work within the renderer. */
    update(props: Props): void | Promise<void>
    /** Stop DOM work immediately. Promise rejection is reported, but remounting does not await cleanup. */
    destroy(): void | Promise<void>
}

export type JsRenderer<Props, Utilities, InitialProps = Props> = (
    context: JsRendererContext<InitialProps, Utilities>,
) => JsRendererInstance<Props> | Promise<JsRendererInstance<Props>>
