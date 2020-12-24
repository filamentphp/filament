@extends('filament::layouts.field-group')

@pushonce('head')
    <link rel="stylesheet" href="https://www.unpkg.com/trix@1.3.1/dist/trix.css">
@endpushonce

@pushonce('js')
    <script src="https://www.unpkg.com/trix@1.3.1/dist/trix.js"></script>
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

@section('field')
    <div 
        x-data="{ content: @entangle($field->model).defer }" 
        x-cloak
    >
        <input 
            type="hidden" 
            value="{{ $field->value }}"
            id="value-{{ $field->id }}"                 
        />
    
        <div 
            wire:ignore
            @trix-change="content = $event.target.value"
            @trix-file-accept="$event.preventDefault()"
        >
            <trix-toolbar id="toolbar-{{ $field->id }}">
                <div class="trix-button-row">
                    <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="Bold" tabindex="-1">Bold</button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="Italic" tabindex="-1">Italic</button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="Strikethrough" tabindex="-1">Strikethrough</button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" title="Link" tabindex="-1">Link</button>
                    </span>
            
                    <span class="trix-button-group trix-button-group--heading-tools" data-trix-button-group="heading-tools">
                        {{-- <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1" data-trix-attribute="heading1" title="Heading Level 1" tabindex="-1">Heading</button> --}}
                        <button type="button" class="trix-button" data-trix-attribute="heading" title="Heading Level 2" tabindex="-1">H2</button>
                        <button type="button" class="trix-button" data-trix-attribute="subHeading" title="Heading Level 3" tabindex="-1">H3</button>
                    </span>
                    
                    <span class="trix-button-group trix-button-group--block-tools" data-trix-button-group="block-tools">
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-quote" data-trix-attribute="quote" title="Quote" tabindex="-1">Quote</button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-code" data-trix-attribute="code" title="Code" tabindex="-1">Code</button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="Bullets" tabindex="-1">Bullets</button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="Numbers" tabindex="-1">Numbers</button>
                    </span>
            
                    {{-- 
                    <span class="trix-button-group trix-button-group--file-tools" data-trix-button-group="file-tools">
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-attach" data-trix-action="attachFiles" title="Attach Files" tabindex="-1">Attach Files</button>
                    </span>
                    --}}
                    
                    <span class="trix-button-group-spacer"></span>
                    
                    <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="Undo" tabindex="-1" disabled>Undo</button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="Redo" tabindex="-1" disabled>Redo</button>
                    </span>
                </div>
                
                <div class="trix-dialogs" data-trix-dialogs>
                    <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
                        <div class="trix-dialog__link-fields">
                            <input type="url" name="href" class="trix-input trix-input--dialog" placeholder="Enter a URL…" aria-label="URL" required data-trix-input disabled="disabled">
                            <div class="trix-button-group">
                                <input type="button" class="trix-button trix-button--dialog" value="Link" data-trix-method="setAttribute">
                                <input type="button" class="trix-button trix-button--dialog" value="Unlink" data-trix-method="removeAttribute">
                            </div>
                        </div>
                    </div>
                </div>
            </trix-toolbar>

            <trix-editor 
                id="{{ $field->id }}"
                toolbar="toolbar-{{ $field->id }}"
                input="value-{{ $field->id }}" 
                class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-white border-gray-300 prose max-w-none"
                @foreach ($field->extraAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach 
            ></trix-editor>
        </div>
    </div>
@overwrite
