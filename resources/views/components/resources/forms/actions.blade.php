@props([
    'actions' => [],
])

<div class="flex flex-wrap items-center gap-3">
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
