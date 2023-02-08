<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $getState();
    @endphp

    <div {{ $attributes
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-infolists-prose-entry prose max-w-none',
            match ($size = $getSize($state)) {
                'sm' => 'prose-sm',
                'base', 'md', null => 'prose-base',
                'lg' => 'prose-lg',
                default => $size,
            },
        ])
    }}>
        {{ str($state)
            ->when($isMarkdown, fn (\Illuminate\Support\Stringable $stringable) => $stringable->markdown())
            ->sanitizeHtml()
            ->toHtmlString() }}
    </div>
</x-dynamic-component>
