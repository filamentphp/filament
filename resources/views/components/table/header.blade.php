<th {{ $attributes->merge([
        'scope' => 'col',
        'class' => 'px-6 py-3 text-left text-xs font-medium uppercase tracking-wider',
    ]) }}>
    {{ $slot }}
</th>