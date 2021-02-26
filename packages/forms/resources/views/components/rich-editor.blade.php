@pushonce('filament-styles:rich-editor-field')
    <link rel="stylesheet" href="https://unpkg.com/trix@1.3.1/dist/trix.css">

    <style>
        trix-editor:empty:not(:focus)::before {
            @apply text-gray-400 opacity-100;
        }
    </style>
@endpushonce

@pushonce('filament-scripts:rich-editor-field')
    <script src="https://unpkg.com/trix@1.3.1/dist/trix.js"></script>

    <script>
        Trix.config.blockAttributes.heading = {
            tagName: "h2",
            terminal: true,
            breakOnReturn: true,
            group: false
        }

        Trix.config.blockAttributes.subHeading = {
            tagName: "h3",
            terminal: true,
            breakOnReturn: true,
            group: false
        }
    </script>
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->id"
    :help-message="__($formComponent->helpMessage)"
    :hint="__($formComponent->hint)"
    :label="__($formComponent->label)"
    :required="$formComponent->required"
>
    <div
        x-data="{
            value: @entangle($formComponent->name).defer,
        }"
        @unless ($formComponent->disabled)
            x-init="
                $refs.trix?.editor?.loadHTML(value)

                $watch('value', () => {
                    if (document.activeElement === $refs.trix) return

                    $refs.trix?.editor?.loadHTML(value)
                })
            "
            x-on:trix-attachment-add="
                if (! $event.attachment.file) return

                let attachment = $event.attachment

                let formData = new FormData()
                formData.append('directory', '{{ $formComponent->attachmentDirectory }}')
                formData.append('disk', '{{ $formComponent->attachmentDisk }}')
                formData.append('file', attachment.file)

                fetch('{{ $formComponent->attachmentUploadUrl }}', {
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    method: 'POST',
                })
                .then((response) => response.text())
                .then((url) => {
                    attachment.setAttributes({
                        url: url,
                        href: url,
                    })
                })
            "
            x-on:trix-change="value = $event.target.value"
        @endunless
        x-cloak
        wire:ignore
    >
        @unless ($formComponent->disabled)
            <input id="trix-value-{{ $formComponent->id }}" type="hidden" />

            <trix-toolbar id="trix-toolbar-{{ $formComponent->id }}">
                <div class="trix-button-row">
                    @if ($formComponent->hasToolbarButton(['bold', 'italic', 'strike', 'link']))
                        <span class="trix-button-group trix-button-group--text-tools"
                              data-trix-button-group="text-tools">
                            @if ($formComponent->hasToolbarButton('bold'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-bold"
                                        data-trix-attribute="bold" data-trix-key="b" title="{{ __('forms::fields.richEditor.toolbarButtons.bold') }}"
                                        tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.bold') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('italic'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-italic"
                                        data-trix-attribute="italic" data-trix-key="i" title="{{ __('forms::fields.richEditor.toolbarButtons.italic') }}"
                                        tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.italic') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('strike'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-strike"
                                        data-trix-attribute="strike" title="{{ __('forms::fields.richEditor.toolbarButtons.strike') }}"
                                        tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.strike') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('link'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-link"
                                        data-trix-attribute="href" data-trix-action="link" data-trix-key="k"
                                        title="{{ __('forms::fields.richEditor.toolbarButtons.link') }}"
                                        tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.link') }}</button>
                            @endif
                        </span>
                    @endif

                    @if ($formComponent->hasToolbarButton(['heading1', 'heading', 'subHeading']))
                        <span class="trix-button-group trix-button-group--heading-tools"
                              data-trix-button-group="heading-tools">
                            @if ($formComponent->hasToolbarButton('title'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1"
                                        data-trix-attribute="heading1" title="{{ __('forms::fields.richEditor.toolbarButtons.title') }}"
                                        tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.title') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('heading'))
                                <button type="button" class="trix-button" data-trix-attribute="heading"
                                        title="{{ __('forms::fields.richEditor.toolbarButtons.heading') }}" tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.heading') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('subheading'))
                                <button type="button" class="trix-button" data-trix-attribute="subHeading"
                                        title="{{ __('forms::fields.richEditor.toolbarButtons.subheading') }}" tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.subheading') }}</button>
                            @endif
                        </span>
                    @endif

                    @if ($formComponent->hasToolbarButton(['quote', 'code', 'bullet', 'number']))
                        <span class="trix-button-group trix-button-group--block-tools"
                              data-trix-button-group="block-tools">
                            @if ($formComponent->hasToolbarButton('quote'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-quote"
                                        data-trix-attribute="quote" title="{{ __('forms::fields.richEditor.toolbarButtons.quote') }}" tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.quote') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('code'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-code"
                                        data-trix-attribute="code" title="{{ __('forms::fields.richEditor.toolbarButtons.code') }}" tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.code') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('bullet'))
                                <button type="button"
                                        class="trix-button trix-button--icon trix-button--icon-bullet-list"
                                        data-trix-attribute="bullet" title="{{ __('forms::fields.richEditor.toolbarButtons.bullet') }}" tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.bullet') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('number'))
                                <button type="button"
                                        class="trix-button trix-button--icon trix-button--icon-number-list"
                                        data-trix-attribute="number" title="{{ __('forms::fields.richEditor.toolbarButtons.number') }}" tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.number') }}</button>
                            @endif
                        </span>
                    @endif

                    @if ($formComponent->hasToolbarButton('attachFiles'))
                        <span class="trix-button-group trix-button-group--file-tools"
                              data-trix-button-group="file-tools">
                            <button type="button" class="trix-button trix-button--icon trix-button--icon-attach"
                                    data-trix-action="attachFiles" title="{{ __('forms::fields.richEditor.toolbarButtons.attachFiles') }}"
                                    tabindex="-1">{{ __('forms::fields.richEditor.toolbarButtons.attachFiles') }}</button>
                        </span>
                    @endif

                    @if ($formComponent->hasToolbarButton(['undo', 'redo']))
                        <span class="trix-button-group-spacer"></span>

                        <span class="trix-button-group trix-button-group--history-tools"
                              data-trix-button-group="history-tools">
                            @if ($formComponent->hasToolbarButton('undo'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-undo"
                                        data-trix-action="undo" data-trix-key="z" title="{{ __('forms::fields.richEditor.toolbarButtons.undo') }}" tabindex="-1" disabled>{{ __('forms::fields.richEditor.toolbarButtons.undo') }}</button>
                            @endif

                            @if ($formComponent->hasToolbarButton('redo'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-redo"
                                        data-trix-action="redo" data-trix-key="shift+z" title="{{ __('forms::fields.richEditor.toolbarButtons.redo') }}" tabindex="-1"
                                        disabled>{{ __('forms::fields.richEditor.toolbarButtons.redo') }}</button>
                            @endif
                        </span>
                    @endif
                </div>

                <div class="trix-dialogs" data-trix-dialogs>
                    <div class="trix-dialog trix-dialog--link" data-trix-dialog="href"
                         data-trix-dialog-attribute="href">
                        <div class="trix-dialog__link-fields">
                            <input type="url" name="href" class="trix-input trix-input--dialog"
                                   placeholder="{{ __('forms::fields.richEditor.dialogs.link.placeholder') }}" aria-label="{{ __('forms::fields.richEditor.dialogs.link.label') }}" required data-trix-input
                                   disabled="disabled">

                            <div class="trix-button-group">
                                <input type="button" class="trix-button trix-button--dialog" value="{{ __('forms::fields.richEditor.dialogs.link.buttons.link') }}"
                                       data-trix-method="setAttribute">

                                <input type="button" class="trix-button trix-button--dialog" value="{{ __('forms::fields.richEditor.dialogs.link.buttons.unlink') }}"
                                       data-trix-method="removeAttribute">
                            </div>
                        </div>
                    </div>
                </div>
            </trix-toolbar>

            <trix-editor
                {{ $formComponent->autofocus ? 'autofocus' : null }}
                id="{{ $formComponent->id }}"
                input="trix-value-{{ $formComponent->id }}"
                placeholder="{{ __($formComponent->placeholder) }}"
                toolbar="trix-toolbar-{{ $formComponent->id }}"
                x-ref="trix"
                class="block w-full prose placeholder-gray-400 placeholder-opacity-100 bg-white border-gray-300 rounded shadow-sm focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 max-w-none"
                {!! Filament\format_attributes($formComponent->extraAttributes) !!}
            />
        @else
            <div x-html="value" class="p-3 prose border border-gray-300 rounded shadow-sm"></div>
        @endunless
    </div>
</x-forms::field-group>
