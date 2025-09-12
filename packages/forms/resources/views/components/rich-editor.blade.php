@php
    use Filament\Support\Facades\FilamentView;

    $id = $getId();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @if ($isDisabled)
        <div
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            }"
            x-html="state"
            class="fi-fo-rich-editor fi-disabled prose block w-full max-w-none rounded-lg bg-gray-50 px-3 py-3 text-gray-500 shadow-sm ring-1 ring-gray-950/10 dark:prose-invert dark:bg-transparent dark:text-gray-400 dark:ring-white/10 sm:text-sm"
        ></div>
    @else
        <x-filament::input.wrapper
            :valid="! $errors->has($statePath)"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                    ->class(['fi-fo-rich-editor max-w-full overflow-x-auto'])
            "
        >
            <div
                @if (FilamentView::hasSpaMode())
                    {{-- format-ignore-start --}}x-load="visible || event (ax-modal-opened)"{{-- format-ignore-end --}}
                @else
                    x-load
                @endif
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('rich-editor', 'filament/forms') }}"
                x-data="richEditorFormComponent({
                            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                        })"
                x-on:trix-attachment-add="
                    if (! $event.attachment.file) return

                    let attachment = $event.attachment

                    $wire.upload(
                        `componentFileAttachments.{{ $statePath }}`,
                        attachment.file,
                        () => {
                            $wire
                                .getFormComponentFileAttachmentUrl('{{ $statePath }}')
                                .then((url) => {
                                    attachment.setAttributes({
                                        url: url,
                                        href: url,
                                    })
                                })
                        },
                    )
                "
                x-on:trix-change="
                    let value = $event.target.value

                    $nextTick(() => {
                        if (! $refs.trix) {
                            return
                        }

                        state = value
                    })
                "
                @if ($isLiveDebounced())
                    x-on:trix-change.debounce.{{ $getLiveDebounce() }}="
                        $nextTick(() => {
                            if (! $refs.trix) {
                                return
                            }

                            $wire.call('$refresh')
                        })
                    "
                @endif
                @if (! $hasToolbarButton('attachFiles'))
                    x-on:trix-file-accept="$event.preventDefault()"
                @endif
                {{ $getExtraAlpineAttributeBag() }}
            >
                <input
                    id="trix-value-{{ $id }}"
                    x-ref="trixValue"
                    type="hidden"
                />

                <trix-toolbar
                    id="trix-toolbar-{{ $id }}"
                    @class([
                        'fi-fo-rich-editor-toolbar relative flex flex-col gap-x-3 border-b border-gray-100 px-2.5 py-2 dark:border-white/10',
                        'hidden' => ! count($getToolbarButtons()),
                    ])
                >
                    <div class="flex gap-x-3 overflow-x-auto">
                        @if ($hasToolbarButton(['bold', 'italic', 'underline', 'strike', 'link']))
                            <x-filament-forms::rich-editor.toolbar.group
                                data-trix-button-group="text-tools"
                            >
                                @if ($hasToolbarButton('bold'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="bold"
                                        data-trix-key="b"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.bold') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-bold"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('italic'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="italic"
                                        data-trix-key="i"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.italic') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-italic"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('underline'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="underline"
                                        data-trix-key="u"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.underline') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-underline"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('strike'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="strike"
                                        data-trix-key="s"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.strike') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-strikethrough"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('link'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="href"
                                        data-trix-action="link"
                                        data-trix-key="k"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.link') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-link"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif
                            </x-filament-forms::rich-editor.toolbar.group>
                        @endif

                        @if ($hasToolbarButton(['h1', 'h2', 'h3']))
                            <x-filament-forms::rich-editor.toolbar.group
                                data-trix-button-group="heading-tools"
                            >
                                @if ($hasToolbarButton('h1'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="heading1"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h1') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-h1"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('h2'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="heading"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h2') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-h2"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('h3'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="subHeading"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h3') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-h3"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif
                            </x-filament-forms::rich-editor.toolbar.group>
                        @endif

                        @if ($hasToolbarButton(['blockquote', 'codeBlock', 'bulletList', 'orderedList']))
                            <x-filament-forms::rich-editor.toolbar.group
                                data-trix-button-group="block-tools"
                            >
                                @if ($hasToolbarButton('blockquote'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="quote"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.blockquote') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-chat-bubble-bottom-center-text"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('codeBlock'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="code"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.code_block') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-code-bracket"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('bulletList'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="bullet"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.bullet_list') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-list-bullet"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('orderedList'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-attribute="number"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.ordered_list') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-numbered-list"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif
                            </x-filament-forms::rich-editor.toolbar.group>
                        @endif

                        @if ($hasToolbarButton('attachFiles'))
                            <x-filament-forms::rich-editor.toolbar.group
                                data-trix-button-group="file-tools"
                            >
                                <x-filament-forms::rich-editor.toolbar.button
                                    data-trix-action="attachFiles"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.attach_files') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        icon="heroicon-m-photo"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            </x-filament-forms::rich-editor.toolbar.group>
                        @endif

                        @if ($hasToolbarButton(['undo', 'redo']))
                            <x-filament-forms::rich-editor.toolbar.group
                                data-trix-button-group="history-tools"
                            >
                                @if ($hasToolbarButton('undo'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-action="undo"
                                        data-trix-key="z"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.undo') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-arrow-uturn-left"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif

                                @if ($hasToolbarButton('redo'))
                                    <x-filament-forms::rich-editor.toolbar.button
                                        data-trix-action="redo"
                                        data-trix-key="shift+z"
                                        title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.redo') }}"
                                        tabindex="-1"
                                    >
                                        <x-filament::icon
                                            icon="heroicon-m-arrow-uturn-right"
                                            class="h-5 w-5"
                                        />
                                    </x-filament-forms::rich-editor.toolbar.button>
                                @endif
                            </x-filament-forms::rich-editor.toolbar.group>
                        @endif
                    </div>

                    <div x-cloak data-trix-dialogs class="trix-dialogs">
                        <div
                            data-trix-dialog="href"
                            data-trix-dialog-attribute="href"
                            class="trix-dialog trix-dialog--link"
                        >
                            <div class="trix-dialog__link-fields">
                                <input
                                    aria-label="{{ __('filament-forms::components.rich_editor.dialogs.link.label') }}"
                                    data-trix-input
                                    disabled
                                    name="href"
                                    placeholder="{{ __('filament-forms::components.rich_editor.dialogs.link.placeholder') }}"
                                    required
                                    type="text"
                                    inputmode="url"
                                    class="trix-input trix-input--dialog"
                                />

                                <div class="trix-button-group">
                                    <input
                                        data-trix-method="setAttribute"
                                        type="button"
                                        value="{{ __('filament-forms::components.rich_editor.dialogs.link.actions.link') }}"
                                        class="trix-button trix-button--dialog"
                                    />

                                    <input
                                        data-trix-method="removeAttribute"
                                        type="button"
                                        value="{{ __('filament-forms::components.rich_editor.dialogs.link.actions.unlink') }}"
                                        class="trix-button trix-button--dialog"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </trix-toolbar>

                <trix-editor
                    @if ($isAutofocused())
                        autofocus
                    @endif
                    id="{{ $id }}"
                    input="trix-value-{{ $id }}"
                    placeholder="{{ $getPlaceholder() }}"
                    toolbar="trix-toolbar-{{ $id }}"
                    @if ($isLiveOnBlur())
                        x-on:blur="$wire.call('$refresh')"
                    @endif
                    x-ref="trix"
                    wire:ignore
                    wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.{{
                        substr(md5(serialize([
                            $isDisabled,
                        ])), 0, 64)
                    }}"
                    @if ($isGrammarlyDisabled())
                        data-gramm="false"
                        data-gramm_editor="false"
                        data-enable-grammarly="false"
                    @endif
                    {{
                        $getExtraInputAttributeBag()->class([
                            'fi-fo-rich-editor-editor prose min-h-[theme(spacing.48)] max-w-none !border-none px-3 py-1.5 text-base text-gray-950 dark:prose-invert focus-visible:outline-none dark:text-white sm:text-sm sm:leading-6',
                        ])
                    }}
                ></trix-editor>
            </div>
        </x-filament::input.wrapper>
    @endif
</x-dynamic-component>
