{{-- @deprecated Copy the code from this view directly into your own view to improve performance. --}}

@props([
    'components',
    'record',
    'recordKey' => null,
    'rowLoop' => null,
])

@foreach ($components as $layoutComponent)
    {{
        $layoutComponent
            ->record($record)
            ->recordKey($recordKey)
            ->rowLoop($rowLoop)
            ->renderInLayout()
    }}
@endforeach
