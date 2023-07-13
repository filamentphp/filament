<div
    {{
        $attributes
            ->merge([
                'aria-hidden' => 'true',
            ], escape: false)
            ->class(['fi-hr border-t dark:border-gray-700'])
    }}
></div>
