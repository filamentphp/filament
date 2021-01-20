<div
    {{ $attributes->merge(['class' => 'p-4 md:p-6']) }}
     x-show="tab === '{{ $id }}'"
     tabindex="0"
     role="tabpanel"
     id="{{ $id }}-tab"
     aria-labelledby="{{ $id }}"
>
    {{ $slot }}
</div>
