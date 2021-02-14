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

<x-filament::field-group
    :error-key="$field->name"
    :for="$field->id"
    :help-message="__($field->helpMessage)"
    :hint="__($field->hint)"
    :label="__($field->label)"
    :required="$field->required"
>
    <div
        x-data="{
            value: @entangle($field->name).defer,
            @unless ($field->disabled)
                isFocused() { return document.activeElement !== this.$refs.trix },
                setValue() { this.$refs.trix.editor?.loadHTML(this.value) },
            @endunless
        }"
        @unless ($field->disabled)
        x-init="setValue(); $watch('value', () => isFocused() && setValue())"
        x-on:trix-attachment-add="
            if (! $event.attachment.file) return

            let attachment = $event.attachment

            let formData = new FormData()
            formData.append('directory', '{{ $field->attachmentDirectory }}')
            formData.append('disk', '{{ $field->attachmentDisk }}')
            formData.append('file', attachment.file)

            fetch('{{ $field->attachmentUploadUrl }}', {
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
        @unless ($field->disabled)
            <input id="trix-value-{{ $field->id }}" type="hidden" />

            <trix-toolbar id="trix-toolbar-{{ $field->id }}">
                <div class="trix-button-row">
                    @if ($field->hasToolbarButton(['bold', 'italic', 'strike', 'link']))
                        <span class="trix-button-group trix-button-group--text-tools"
                              data-trix-button-group="text-tools">
                            @if ($field->hasToolbarButton('bold'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-bold"
                                        data-trix-attribute="bold" data-trix-key="b" title="Bold"
                                        tabindex="-1">Bold</button>
                            @endif

                            @if ($field->hasToolbarButton('italic'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-italic"
                                        data-trix-attribute="italic" data-trix-key="i" title="Italic"
                                        tabindex="-1">Italic</button>
                            @endif

                            @if ($field->hasToolbarButton('strike'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-strike"
                                        data-trix-attribute="strike" title="Strikethrough"
                                        tabindex="-1">Strikethrough</button>
                            @endif

                            @if ($field->hasToolbarButton('link'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-link"
                                        data-trix-attribute="href" data-trix-action="link" data-trix-key="k"
                                        title="Link"
                                        tabindex="-1">Link</button>
                            @endif
                        </span>
                    @endif

                    @if ($field->hasToolbarButton(['heading1', 'heading', 'subHeading']))
                        <span class="trix-button-group trix-button-group--heading-tools"
                              data-trix-button-group="heading-tools">
                            @if ($field->hasToolbarButton('heading1'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1"
                                        data-trix-attribute="heading1" title="Heading Level 1"
                                        tabindex="-1">Heading</button>
                            @endif

                            @if ($field->hasToolbarButton('heading'))
                                <button type="button" class="trix-button" data-trix-attribute="heading"
                                        title="Heading Level 2" tabindex="-1">H2</button>
                            @endif

                            @if ($field->hasToolbarButton('subHeading'))
                                <button type="button" class="trix-button" data-trix-attribute="subHeading"
                                        title="Heading Level 3" tabindex="-1">H3</button>
                            @endif
                        </span>
                    @endif

                    @if ($field->hasToolbarButton(['quote', 'code', 'bullet', 'number']))
                        <span class="trix-button-group trix-button-group--block-tools"
                              data-trix-button-group="block-tools">
                            @if ($field->hasToolbarButton('quote'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-quote"
                                        data-trix-attribute="quote" title="Quote" tabindex="-1">Quote</button>
                            @endif

                            @if ($field->hasToolbarButton('code'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-code"
                                        data-trix-attribute="code" title="Code" tabindex="-1">Code</button>
                            @endif

                            @if ($field->hasToolbarButton('bullet'))
                                <button type="button"
                                        class="trix-button trix-button--icon trix-button--icon-bullet-list"
                                        data-trix-attribute="bullet" title="Bullets" tabindex="-1">Bullets</button>
                            @endif

                            @if ($field->hasToolbarButton('number'))
                                <button type="button"
                                        class="trix-button trix-button--icon trix-button--icon-number-list"
                                        data-trix-attribute="number" title="Numbers" tabindex="-1">Numbers</button>
                            @endif
                        </span>
                    @endif

                    @if ($field->hasToolbarButton('attachFiles'))
                        <span class="trix-button-group trix-button-group--file-tools"
                              data-trix-button-group="file-tools">
                            <button type="button" class="trix-button trix-button--icon trix-button--icon-attach"
                                    data-trix-action="attachFiles" title="Attach Files"
                                    tabindex="-1">Attach Files</button>
                        </span>
                    @endif

                    @if ($field->hasToolbarButton(['undo', 'redo']))
                        <span class="trix-button-group-spacer"></span>

                        <span class="trix-button-group trix-button-group--history-tools"
                              data-trix-button-group="history-tools">
                            @if ($field->hasToolbarButton('undo'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-undo"
                                        data-trix-action="undo" data-trix-key="z" title="Undo" tabindex="-1" disabled>Undo</button>
                            @endif

                            @if ($field->hasToolbarButton('redo'))
                                <button type="button" class="trix-button trix-button--icon trix-button--icon-dangero"
                                        data-trix-action="redo" data-trix-key="shift+z" title="Redo" tabindex="-1"
                                        disabled>Redo</button>
                            @endif
                        </span>
                    @endif
                </div>

                <div class="trix-dialogs" data-trix-dialogs>
                    <div class="trix-dialog trix-dialog--link" data-trix-dialog="href"
                         data-trix-dialog-attribute="href">
                        <div class="trix-dialog__link-fields">
                            <input type="url" name="href" class="trix-input trix-input--dialog"
                                   placeholder="Enter a URL…" aria-label="URL" required data-trix-input
                                   disabled="disabled">

                            <div class="trix-button-group">
                                <input type="button" class="trix-button trix-button--dialog" value="Link"
                                       data-trix-method="setAttribute">

                                <input type="button" class="trix-button trix-button--dialog" value="Unlink"
                                       data-trix-method="removeAttribute">
                            </div>
                        </div>
                    </div>
                </div>
            </trix-toolbar>

            <trix-editor
                @if ($field->autofocus) autofocus @endif
                id="{{ $field->id }}"
                input="trix-value-{{ $field->id }}"
                placeholder="{{ __($field->placeholder) }}"
                toolbar="trix-toolbar-{{ $field->id }}"
                x-ref="trix"
                class="block w-full rounded shadow-sm placeholder-gray-400 placeholder-opacity-100 focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 bg-white border-gray-300 prose max-w-none"
                {{ Filament\format_attributes($field->extraAttributes) }}
            />
        @else
            <div x-html="value" class="p-3 rounded shadow-sm border border-gray-300 prose"></div>
        @endunless
    </div>
</x-filament::field-group>
