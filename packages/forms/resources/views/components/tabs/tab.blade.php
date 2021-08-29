<div
    aria-labelledby="{{ $getId() }}"
    id="{{ $getId() }}"
    role="tabpanel"
    tabindex="0"
    x-show="tab === '{{ $getId() }}'"
    class="p-6 focus:outline-none"
>
    {{ $getChildComponentContainer() }}
</div>