<ul
    {{
        $getExtraAttributeBag()->class([
            'fi-sc-unordered-list',
            (($size = $getSize()) instanceof \Filament\Support\Enums\TextSize) ? "fi-size-{$size->value}" : $size,
        ])
    }}
>
    @foreach ($getChildSchema()->getComponents() as $component)
        <li>
            {{ $component }}
        </li>
    @endforeach
</ul>
