<div {{
    $attributes
        ->merge([
            'id' => $getId(),
        ], escape: false)
        ->merge($getExtraAttributes(), escape: false)
        ->class(['filament-infolists-card-component p-6 bg-white rounded-xl ring-1 ring-gray-900/10 dark:ring-gray-50/10 dark:bg-gray-800'])
}}>
    {{ $getChildComponentContainer() }}
</div>
