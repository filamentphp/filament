<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'flex gap-6',
                match ($getFromBreakpoint()) {
                    'sm' => 'flex-col sm:flex-row sm:items-center',
                    'md' => 'flex-col md:flex-row md:items-center',
                    'lg' => 'flex-col lg:flex-row lg:items-center',
                    'xl' => 'flex-col xl:flex-row xl:items-center',
                    '2xl' => 'flex-col 2xl:flex-row 2xl:items-center',
                    default => 'items-center',
                },
            ])
    }}
>
    @foreach ($getChildComponentContainers() as $container)
        @foreach ($container->getComponents() as $component)
            <div
                @class([
                    'w-full flex-1' => $component->canGrow(),
                ])
            >
                {{ $component }}
            </div>
        @endforeach
    @endforeach
</div>
