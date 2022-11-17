<div {{ $attributes
    ->merge([
        'aria-hidden' => 'true',
    ], escape: false)
    ->class(['filament-hr border-t dark:border-gray-700'])
}}></div>
