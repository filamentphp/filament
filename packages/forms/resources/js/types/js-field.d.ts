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

export interface JsFieldProps<
    Value = unknown,
    Configuration = Record<string, unknown>,
> {
    readonly value: Snapshot<Value>
    readonly configuration: Snapshot<Configuration>
    readonly id: string
    readonly ariaDescribedBy: string
    readonly isDisabled: boolean
    readonly isReadOnly: boolean
    readonly isRequired: boolean
    readonly isInvalid: boolean
    readonly isLive: boolean
    readonly isLiveOnBlur: boolean
    readonly debounce: number | null
}

export interface JsFieldInitialProps<
    Value = unknown,
    Configuration = Record<string, unknown>,
> extends JsFieldProps<Value, Configuration> {
    readonly onChange: (value: Value | Snapshot<Value>) => void
    readonly onBlur: () => void
}

/** Common proxy methods. The runtime object is Livewire's unmodified `$wire`. */
export interface JsFieldLivewire extends JsRendererLivewire {}

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
    Configuration = Record<string, unknown>,
    Methods = Record<never, never>,
> extends JsRendererContext<
    JsFieldInitialProps<Value, Configuration>,
    JsFieldUtilities<Value> & Methods
> {}

export interface JsFieldRendererInstance<
    Value = unknown,
    Configuration = Record<string, unknown>,
> extends JsRendererInstance<JsFieldProps<Value, Configuration>> {}

export type JsFieldRenderer<
    Value = unknown,
    Configuration = Record<string, unknown>,
    Methods = Record<never, never>,
> = (
    context: JsFieldRendererContext<Value, Configuration, Methods>,
) =>
    | JsFieldRendererInstance<Value, Configuration>
    | Promise<JsFieldRendererInstance<Value, Configuration>>
