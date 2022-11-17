<div {{ $attributes
    ->merge([
        'aria-hidden' => 'true',
    ], escape: true)
    ->class(['filament-hr border-t dark:border-gray-700'])
}}></div>
