<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $attributes
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-infolists-prose-entry prose max-w-none',
            match ($size = $getSize()) {
                'sm', null => 'prose-sm',
                'base', 'md' => 'prose-base',
                'lg' => 'prose-lg',
                default => $size,
            },
        ])
    }}>
        {{ str($getState())
            ->when($isMarkdown, fn (\Illuminate\Support\Stringable $stringable) => $stringable->markdown())
            ->sanitizeHtml()
            ->toHtmlString() }}
    </div>
</x-dynamic-component>
