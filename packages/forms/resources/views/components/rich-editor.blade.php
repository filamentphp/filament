<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="richEditorFormComponent({
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        x-on:trix-change="state = $event.target.value"
        x-on:trix-attachment-add="
            if (! $event.attachment.file) return

            let attachment = $event.attachment

            $wire.upload(`componentFileAttachments.{{ $getStatePath() }}`, attachment.file, () => {
                $wire.getComponentFileAttachmentUrl('{{ $getStatePath() }}').then((url) => {
                    attachment.setAttributes({
                        url: url,
                        href: url,
                    })
                })
            })
        "
        x-cloak
        wire:ignore
        {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-rich-editor-component']) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        @unless ($isDisabled())
            <input id="trix-value-{{ $getId() }}" type="hidden" />

            <trix-toolbar id="trix-toolbar-{{ $getId() }}">
                <div class="trix-button-row">
                    @if ($hasToolbarButton(['bold', 'italic', 'strike', 'link']))
                        <span data-trix-button-group="text-tools" class="trix-button-group trix-button-group--text-tools">
                            @if ($hasToolbarButton('bold'))
                                <button
                                    data-trix-attribute="bold"
                                    data-trix-key="b"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.bold') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-bold"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.bold') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('italic'))
                                <button
                                    data-trix-attribute="italic"
                                    data-trix-key="i"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.italic') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-italic"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.italic') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('strike'))
                                <button
                                    data-trix-attribute="strike"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.strike') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-strike"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.strike') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('link'))
                                <button
                                    data-trix-attribute="href"
                                    data-trix-action="link"
                                    data-trix-key="k"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.link') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-link"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.link') }}
                                </button>
                            @endif
                        </span>
                    @endif

                    @if ($hasToolbarButton(['h1', 'h2', 'h3']))
                        <span data-trix-button-group="heading-tools" class="trix-button-group trix-button-group--heading-tools">
                            @if ($hasToolbarButton('h1'))
                                <button
                                    data-trix-attribute="heading1"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.h1') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-heading-1"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.h1') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('h2'))
                                <button
                                    data-trix-attribute="heading"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.h2') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.h2') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('h3'))
                                <button
                                    data-trix-attribute="subHeading"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.h3') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.h3') }}
                                </button>
                            @endif
                        </span>
                    @endif

                    @if ($hasToolbarButton(['blockquote', 'codeBlock', 'bulletList', 'orderedList']))
                        <span data-trix-button-group="block-tools" class="trix-button-group trix-button-group--block-tools">
                            @if ($hasToolbarButton('blockquote'))
                                <button
                                    data-trix-attribute="quote"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.blockquote') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-quote"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.blockquote') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('codeBlock'))
                                <button
                                    data-trix-attribute="code"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.code_block') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-code"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.code_block') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('bulletList'))
                                <button
                                    data-trix-attribute="bullet"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.bullet_list') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-bullet-list"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.bullet_list') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('orderedList'))
                                <button
                                    data-trix-attribute="number"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.ordered_list') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-number-list"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.ordered_list') }}
                                </button>
                            @endif
                        </span>
                    @endif

                    @if ($hasToolbarButton('attachFiles'))
                        <span data-trix-button-group="file-tools" class="trix-button-group trix-button-group--file-tools">
                            <button
                                data-trix-action="attachFiles"
                                title="{{ __('forms::components.rich_editor.toolbar_buttons.attach_files') }}"
                                tabindex="-1"
                                type="button"
                                class="trix-button trix-button--icon trix-button--icon-attach"
                            >
                                {{ __('forms::components.rich_editor.toolbar_buttons.attach_files') }}
                            </button>
                        </span>
                    @endif

                    @if ($hasToolbarButton(['undo', 'redo']))
                        <span class="trix-button-group-spacer"></span>

                        <span
                            data-trix-button-group="history-tools"
                            class="trix-button-group trix-button-group--history-tools"
                        >
                            @if ($hasToolbarButton('undo'))
                                <button
                                    data-trix-action="undo"
                                    data-trix-key="z"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.undo') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-undo"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.undo') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('redo'))
                                <button
                                    data-trix-action="redo"
                                    data-trix-key="shift+z"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.redo') }}"
                                    tabindex="-1"
                                    type="button"
                                    class="trix-button trix-button--icon trix-button--icon-redo"
                                >
                                    {{ __('forms::components.rich_editor.toolbar_buttons.redo') }}
                                </button>
                            @endif
                        </span>
                    @endif
                </div>

                <div data-trix-dialogs class="trix-dialogs">
                    <div
                        data-trix-dialog="href"
                        data-trix-dialog-attribute="href"
                        class="trix-dialog trix-dialog--link"
                    >
                        <div class="trix-dialog__link-fields">
                            <input
                                name="href"
                                placeholder="{{ __('forms::components.rich_editor.dialogs.link.placeholder') }}" aria-label="{{ __('forms::components.rich_editor.dialogs.link.label') }}" required data-trix-input
                                disabled
                                type="url"
                                class="trix-input trix-input--dialog"
                            >

                            <div class="trix-button-group">
                                <input
                                    value="{{ __('forms::components.rich_editor.dialogs.link.buttons.link') }}"
                                    data-trix-method="setAttribute"
                                    type="button"
                                    class="trix-button trix-button--dialog"
                                >

                                <input
                                    value="{{ __('forms::components.rich_editor.dialogs.link.buttons.unlink') }}"
                                    data-trix-method="removeAttribute"
                                    type="button"
                                    class="trix-button trix-button--dialog"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </trix-toolbar>

            <trix-editor
                {!! $isAutofocused() ? 'autofocus' : null !!}
                id="{{ $getId() }}"
                input="trix-value-{{ $getId() }}"
                placeholder="{{ $getPlaceholder() }}"
                toolbar="trix-toolbar-{{ $getId() }}"
                x-ref="trix"
                @class([
                    'bg-white block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-600 prose max-w-none',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                ])
            />
        @else
            <div x-html="state" class="p-3 prose border border-gray-300 rounded shadow-sm"></div>
        @endunless
    </div>
</x-forms::field-wrapper>
