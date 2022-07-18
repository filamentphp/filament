<div
    @class([
        'filament-notifications pointer-events-none fixed inset-6 z-30 mx-auto flex flex-col',
        'items-start' => config('notifications.layout.alignment.horizontal') === 'left',
        'items-center' => config('notifications.layout.alignment.horizontal') === 'center',
        'items-end' => config('notifications.layout.alignment.horizontal') === 'right',
        'justify-start' => config('notifications.layout.alignment.vertical') === 'top',
        'justify-center' => config('notifications.layout.alignment.vertical') === 'center',
        'justify-end' => config('notifications.layout.alignment.vertical') === 'bottom',
        match (config('notifications.layout.max_width')) {
            'sm' => 'max-w-screen-sm',
            'md' => 'max-w-screen-md',
            'lg' => 'max-w-screen-lg',
            'xl' => 'max-w-screen-xl',
            '2xl' => 'max-w-screen-2xl',
            default => 'max-w-full',
        },
    ])
>
    @foreach($notifications as $notification)
        {{ $notification }}
    @endforeach
</div>
