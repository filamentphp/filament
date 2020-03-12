<form method="{{ $method }}" action="{{ $action }}" {{ $attributes }}>
    @csrf
    @method($httpVerb)
    {{ $slot }}
    @if ($hint)
        <p class="mt-4 text-sm font-medium leading-5 text-gray-500">{{ $hint }}</p>
    @endif
</form>