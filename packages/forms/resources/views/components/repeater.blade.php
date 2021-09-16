<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div class="space-y-4">
        @if (count($containers = $getChildComponentContainers()))
            <ul class="space-y-4">
                @foreach ($containers as $uuid => $item)
                    <li
                        wire:key="{{ $item->getStatePath() }}"
                        class="flex"
                    >
                        <div class="w-8">
                            <div class="bg-white divide-y shadow-sm rounded-l-lg border-b border-l border-t border-gray-300 overflow-hidden">
                                @unless ($loop->first)
                                    <button
                                        wire:click="dispatchFormEvent('repeater.moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        class="w-full flex items-center justify-center h-8 text-gray-800 transition hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.move_item_up.label') }}
                                        </span>

                                        <x-heroicon-s-chevron-up class="w-5 h-5" />
                                    </button>
                                @endunless

                                @unless ($loop->last)
                                    <button
                                        wire:click="dispatchFormEvent('repeater.moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        class="w-full flex items-center justify-center h-8 text-gray-800 transition hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.move_item_down.label') }}
                                        </span>

                                        <x-heroicon-s-chevron-down class="w-5 h-5" />
                                    </button>
                                @endunless

                                <button
                                    wire:click="dispatchFormEvent('repeater.deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                    type="button"
                                    class="w-full flex items-center justify-center h-8 text-danger-600 transition hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-danger-600 focus:bg-primary-50 focus:border-primary-600"
                                >
                                    <span class="sr-only">
                                        {{ __('forms::components.repeater.buttons.delete_item.label') }}
                                    </span>

                                    <x-heroicon-s-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <div class="flex-1 p-6 bg-white shadow-sm rounded-r-lg rounded-b-lg border border-gray-300">
                            {{ $item }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <button
            wire:click="dispatchFormEvent('repeater.createItem', '{{ $getStatePath() }}')"
            type="button"
            class="w-full h-9 px-4 inline-flex items-center justify-center font-medium tracking-tight transition rounded-lg text-gray-800 bg-white border hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
        >
            <x-heroicon-s-plus class="w-6 h-6 mr-1 -ml-2" />

            {{ __('forms::components.repeater.buttons.add_item.label', [
                'label' => lcfirst($getLabel()),
            ]) }}
        </button>
    </div>
</x-forms::field-wrapper>
