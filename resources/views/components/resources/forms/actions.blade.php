@props([
    'actions' => [],
])

<div class="space-x-3 rtl:space-x-reverse">
    @foreach ($actions as $button)
        <x-filament::button
            :color="$button->getColor()"
            :href="$button->getUrl()"
            :type="$button->getType()"
            :wire:click="$button->getAction() ?? false"
        >
            {{ __($button->getLabel()) }}
        </x-filament::button>
    @endforeach
</div>
