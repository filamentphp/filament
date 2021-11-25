@props([
    'managers',
    'ownerRecord',
])

<div
    x-data="{ tab: '{{ $managers[0] }}' }"
    x-cloak
    class="space-y-2"
>
    @if (count($managers) > 1)
        <div class="flex justify-center">
            <x-filament::tabs>
                @foreach ($managers as $manager)
                    <button
                        type="button"
                        aria-controls="{{ $manager }}"
                        x-bind:aria-selected="tab === '{{ $manager }}'"
                        x-on:click="tab = '{{ $manager }}'"
                        role="tab"
                        x-bind:tabindex="tab === '{{ $manager }}' ? 0 : -1"
                        x-bind:class="{
                            'hover:text-gray-800 focus:text-primary-600': tab !== '{{ $manager }}',
                            'text-primary-600 shadow bg-white': tab === '{{ $manager }}',
                        }"
                        class="flex whitespace-nowrap items-center h-8 px-5 font-medium transition rounded-lg whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-inset"
                    >
                        {{ $manager::getTitle() }}
                    </button>
                @endforeach
            </x-filament::tabs>
        </div>
    @endif

    @foreach ($managers as $manager)
        <div
            @if (count($managers) > 1)
                id="{{ $manager }}"
                role="tabpanel"
                tabindex="0"
                x-show="tab === '{{ $manager }}'"
            @endif
            class="focus:outline-none"
        >
            @livewire(\Livewire\Livewire::getAlias($manager, $manager::getName()), ['ownerRecord' => $ownerRecord])
        </div>
    @endforeach
</div>
