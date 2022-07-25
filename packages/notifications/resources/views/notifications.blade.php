<div
    @class([
        'filament-notifications pointer-events-none fixed inset-4 z-50 mx-auto flex justify-end gap-3',
        match (config('notifications.layout.alignment.horizontal')) {
            'left' => 'items-start',
            'center' => 'items-center',
            'right' => 'items-end',
        },
        match (config('notifications.layout.alignment.vertical')) {
            'top' => 'flex-col-reverse',
            'bottom' => 'flex-col',
        },
    ])
    role="status"
>
    @foreach ($notifications as $notification)
        {{ $notification }}
    @endforeach
</div>
