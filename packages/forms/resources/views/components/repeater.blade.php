<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['space-y-2', 'filament-forms-repeater-component']) }}>
        @if (count($containers = $getChildComponentContainers()))
            <ul class="space-y-2">
                @foreach ($containers as $uuid => $item)
                    <li
                        wire:key="{{ $item->getStatePath() }}"
                        class="relative p-6 bg-white shadow-sm rounded-lg border border-gray-300"
                    >
                        {{ $item }}

                        @unless ($isItemDeletionDisabled() && ($isItemMovementDisabled() && ($loop->count <= 1)))
                            <div class="absolute top-0 right-0 h-6 flex divide-x rounded-bl-lg rounded-tr-lg border-gray-300 border-b border-l overflow-hidden">
                                @unless ($loop->first || $isItemMovementDisabled())
                                    <button
                                        wire:click="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        class="flex items-center justify-center w-6 text-gray-800 hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.move_item_up.label') }}
                                        </span>

                                        <x-heroicon-s-chevron-up class="w-4 h-4" />
                                    </button>
                                @endunless

                                @unless ($loop->last || $isItemMovementDisabled())
                                    <button
                                        wire:click="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        class="flex items-center justify-center w-6 text-gray-800 hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.move_item_down.label') }}
                                        </span>

                                        <x-heroicon-s-chevron-down class="w-4 h-4" />
                                    </button>
                                @endunless

                                @unless ($isItemDeletionDisabled())
                                    <button
                                        wire:click="dispatchFormEvent('repeater::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        class="flex items-center justify-center w-6 text-danger-600 hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-danger-600 focus:bg-primary-50 focus:border-primary-600"
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.delete_item.label') }}
                                        </span>

                                        <x-heroicon-s-trash class="w-4 h-4" />
                                    </button>
                                @endunless
                            </div>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif

        @if ((blank($getMaxItems()) || ($getMaxItems() > $getItemsCount())) && (! $isItemCreationDisabled()))
            <button
                wire:click="dispatchFormEvent('repeater::createItem', '{{ $getStatePath() }}')"
                type="button"
                class="w-full h-9 px-4 inline-flex space-x-1 items-center justify-center font-medium tracking-tight rounded-lg text-gray-800 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600"
            >
                <x-heroicon-s-plus class="w-5 h-5" />

                <span>
                    {{ $getCreateItemButtonLabel() }}
                </span>
            </button>
        @endif
    </div>
</x-forms::field-wrapper>
