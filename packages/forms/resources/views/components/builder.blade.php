<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div class="space-y-4">
        @if (count($containers = $getChildComponentContainers()))
            <ul>
                @foreach ($containers as $uuid => $item)
                    <li
                        x-data="{ isCreateButtonDropdownOpen: false, isCreateButtonVisible: false }"
                        x-on:click="isCreateButtonVisible = true"
                        x-on:click.away="isCreateButtonVisible = false"
                        wire:key="{{ $item->getStatePath() }}"
                    >
                        <div class="flex">
                            <div class="w-8">
                                <div class="bg-white divide-y shadow-sm rounded-l-lg border-b border-l border-t border-gray-300 overflow-hidden">
                                    @unless ($loop->first || $isItemMovementDisabled())
                                        <button
                                            wire:click="dispatchFormEvent('builder::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                            type="button"
                                            class="w-full flex items-center justify-center h-8 text-gray-800 transition hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
                                        >
                                            <span class="sr-only">
                                                {{ __('forms::components.builder.buttons.move_item_up.label') }}
                                            </span>

                                            <x-heroicon-s-chevron-up class="w-5 h-5" />
                                        </button>
                                    @endunless

                                    @unless ($loop->last || $isItemMovementDisabled())
                                        <button
                                            wire:click="dispatchFormEvent('builder::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                            type="button"
                                            class="w-full flex items-center justify-center h-8 text-gray-800 transition hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
                                        >
                                            <span class="sr-only">
                                                {{ __('forms::components.builder.buttons.move_item_down.label') }}
                                            </span>

                                            <x-heroicon-s-chevron-down class="w-5 h-5" />
                                        </button>
                                    @endunless

                                    <button
                                        wire:click="dispatchFormEvent('builder::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        class="w-full flex items-center justify-center h-8 text-danger-600 transition hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-danger-600 focus:bg-primary-50 focus:border-primary-600"
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.builder.buttons.delete_item.label') }}
                                        </span>

                                        <x-heroicon-s-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <div class="flex-1 p-6 bg-white shadow-sm rounded-r-lg rounded-b-lg border border-gray-300">
                                {{ $item }}
                            </div>
                        </div>

                        @unless ($loop->last)
                            <div class="h-12 flex items-center justify-center">
                                <div
                                    x-show="isCreateButtonVisible || isCreateButtonDropdownOpen"
                                    x-transition
                                    class="relative flex justify-center"
                                >
                                    <button
                                        x-on:click="isCreateButtonDropdownOpen = true"
                                        type="button"
                                        class="flex items-center justify-center h-8 w-8 rounded-full text-gray-800 transition hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50"
                                        x-bind:class="{
                                            'bg-gray-50': isCreateButtonDropdownOpen,
                                        }"
                                    >
                                        <span class="sr-only">
                                            {{ $getCreateItemBetweenButtonLabel() }}
                                        </span>

                                        <x-heroicon-o-plus class="w-5 h-5" />
                                    </button>

                                    <div
                                        x-show="isCreateButtonDropdownOpen"
                                        x-on:click.away="isCreateButtonDropdownOpen = false"
                                        x-transition
                                        class="absolute z-10 mt-9 shadow-xl overflow-hidden rounded-xl w-52"
                                    >
                                        <ul class="py-1 space-y-1 bg-white shadow rounded-xl">
                                            @foreach ($getBlocks() as $block)
                                                <li>
                                                    <button
                                                        wire:click="dispatchFormEvent('builder::createItem', '{{ $getStatePath() }}', '{{ $block->getName() }}', '{{ $uuid }}')"
                                                        x-on:click="isCreateButtonDropdownOpen = false"
                                                        type="button"
                                                        class="flex items-center w-full h-8 px-3 text-sm font-medium focus:outline-none hover:text-white hover:bg-primary-600 focus:bg-primary-700 focus:text-white group"
                                                    >
                                                        @if ($icon = $block->getIcon())
                                                            <x-dynamic-component :component="$icon" class="mr-2 -ml-1 text-primary-500 w-5 h-5 group-hover:text-white group-focus:text-white" />
                                                        @endif

                                                        {{ $block->getLabel() }}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif

        <div x-data="{ isCreateButtonDropdownOpen: false }" class="relative flex justify-center">
            <button
                x-on:click="isCreateButtonDropdownOpen = true"
                type="button"
                class="w-full h-9 px-4 inline-flex items-center justify-center font-medium tracking-tight transition rounded-lg text-gray-800 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
            >
                <x-heroicon-s-plus class="w-6 h-6 mr-1 -ml-2" />

                {{ $getCreateItemButtonLabel() }}
            </button>

            <div
                x-show="isCreateButtonDropdownOpen"
                x-on:click.away="isCreateButtonDropdownOpen = false"
                x-transition
                class="absolute z-10 mt-9 shadow-xl overflow-hidden rounded-xl w-52"
            >
                <ul class="py-1 space-y-1 bg-white shadow rounded-xl">
                    @foreach ($getBlocks() as $block)
                        <li>
                            <button
                                wire:click="dispatchFormEvent('builder::createItem', '{{ $getStatePath() }}', '{{ $block->getName() }}')"
                                x-on:click="isCreateButtonDropdownOpen = false"
                                type="button"
                                class="flex items-center w-full h-8 px-3 text-sm font-medium focus:outline-none hover:text-white hover:bg-primary-600 focus:bg-primary-700 focus:text-white group"
                            >
                                @if ($icon = $block->getIcon())
                                    <x-dynamic-component :component="$icon" class="mr-2 -ml-1 text-primary-500 w-5 h-5 group-hover:text-white group-focus:text-white" />
                                @endif

                                {{ $block->getLabel() }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-forms::field-wrapper>
