<div
    @class([
        'filament-notifications pointer-events-none fixed inset-6 z-50 mx-auto flex flex-col',
        match (config('notifications.layout.alignment.horizontal')) {
            'left' => 'items-start',
            'center' => 'items-center',
            'right' => 'items-end',
        },
        match (config('notifications.layout.alignment.vertical')) {
            'top' => 'justify-start',
            'center' => 'justify-center',
            'bottom' => 'justify-end',
        },
        match (config('notifications.layout.max_width')) {
            'sm' => 'max-w-screen-sm',
            'md' => 'max-w-screen-md',
            'lg' => 'max-w-screen-lg',
            'xl' => 'max-w-screen-xl',
            '2xl' => 'max-w-screen-2xl',
            default => 'max-w-full',
        },
    ])
    role="status"
>
    @foreach($notifications as $notification)
        {{ $notification }}
    @endforeach
</div>
