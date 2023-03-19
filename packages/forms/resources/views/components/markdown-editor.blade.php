<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $id = $getId();
        $statePath = $getStatePath();
    @endphp

    @unless ($isDisabled())
        <div
            x-ignore
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('markdown-editor', 'filament/forms') }}"
            x-data="markdownEditorFormComponent({
                placeholder: @js($getPlaceholder()),
                state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }},
                toolbarButtons: @js($getToolbarButtons()),
                translations: @js(__('filament-forms::components.markdown_editor')),
                uploadFileAttachmentUsing: async (file, onSuccess, onError) => {
                    $wire.upload(`componentFileAttachments.{{ $statePath }}`, file, () => {
                        $wire.getComponentFileAttachmentUrl('{{ $statePath }}').then((url) => {
                            if (! url) {
                                return onError()
                            }

                            onSuccess(url)
                        })
                    })
                },
            })"
            wire:ignore
            {{
                $attributes
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->class(['filament-forms-markdown-editor-component font-mono'])
            }}
        >
            <textarea x-ref="editor"></textarea>
        </div>
    @else
        <div class="prose block w-full max-w-none rounded-lg border border-gray-300 bg-white p-3 opacity-70 shadow-sm dark:prose-invert dark:border-gray-600 dark:bg-gray-700">
            {!! str($getState())->markdown()->sanitizeHtml() !!}
        </div>
    @endunless
</x-dynamic-component>
