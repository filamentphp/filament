/** Values must be JSON-serializable. Dates, functions and `undefined` are not field state. */
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

export interface JsFieldProps<
    Value = unknown,
    Config = Record<string, unknown>,
> {
    readonly value: Snapshot<Value>
    readonly config: Snapshot<Config>
    readonly id: string
    readonly ariaDescribedBy: string
    readonly disabled: boolean
    readonly readOnly: boolean
    readonly required: boolean
    readonly invalid: boolean
    readonly isLive: boolean
    readonly isLiveOnBlur: boolean
    readonly debounce: number | null
}

export interface JsFieldInitialProps<
    Value = unknown,
    Config = Record<string, unknown>,
> extends JsFieldProps<Value, Config> {
    readonly onChange: (value: Value | Snapshot<Value>) => void
    readonly onBlur: () => void
}

/** Common proxy methods. The runtime object is Livewire's unmodified `$wire`. */
export interface JsFieldLivewire {
    $call<Result = unknown>(
        method: string,
        ...parameters: JsonValue[]
    ): Promise<Result>
    $get<Result = unknown>(path: string, reactive?: boolean): Result
    $set(path: string, value: JsonValue, live?: boolean): Promise<unknown>
}

export interface JsFieldUtilities<Value = unknown> {
    readonly $wire: JsFieldLivewire
    $callSchemaComponentMethod<Result = unknown>(
        method: string,
        argumentsObject?: Record<string, JsonValue>,
    ): Promise<Result | null>
    /** Read when needed, rather than destructuring this getter during mounting. */
    readonly $state: Snapshot<Value>
    readonly $statePath: string
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

export interface JsFieldRendererContext<
    Value = unknown,
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> {
    host: HTMLElement
    props: JsFieldInitialProps<Value, Config>
    utilities: JsFieldUtilities<Value> & Methods
}

export interface JsFieldRendererInstance<
    Value = unknown,
    Config = Record<string, unknown>,
> {
    /** Calls may overlap. Cancel or order asynchronous work within the renderer. */
    update(props: JsFieldProps<Value, Config>): void | Promise<void>
    /** Stop DOM work immediately. Promise rejection is reported, but remounting does not await cleanup. */
    destroy(): void | Promise<void>
}

export type JsFieldRenderer<
    Value = unknown,
    Config = Record<string, unknown>,
    Methods = Record<never, never>,
> = (
    context: JsFieldRendererContext<Value, Config, Methods>,
) =>
    | JsFieldRendererInstance<Value, Config>
    | Promise<JsFieldRendererInstance<Value, Config>>
