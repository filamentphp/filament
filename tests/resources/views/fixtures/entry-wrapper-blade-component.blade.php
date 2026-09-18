@props(['entry'])

<x-filament-infolists::entry-wrapper
    :entry="$entry"
    class="entry-wrapper-blade-component"
>
    {{ $slot }}
</x-filament-infolists::entry-wrapper>
