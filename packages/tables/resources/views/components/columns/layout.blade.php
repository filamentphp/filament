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
